<?php

namespace App\Http\Controllers;

use App\Models\SharedFile;
use Illuminate\Http\Request;

class SharedFileController extends Controller
{
    /**
     * Display a listing of shared files.
     */
    public function index(Request $request)
    {
        $query = SharedFile::with('user');

        // Search by original file name
        if ($request->has('search') && $request->search != '') {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        // Filter by category/extension
        if ($request->has('category') && $request->category != '') {
            $cat = strtolower($request->category);
            if ($cat === 'pdf') {
                $query->where('original_name', 'like', '%.pdf');
            } elseif ($cat === 'document') {
                $query->where(function ($q) {
                    $q->where('original_name', 'like', '%.doc')
                      ->orWhere('original_name', 'like', '%.docx')
                      ->orWhere('original_name', 'like', '%.xls')
                      ->orWhere('original_name', 'like', '%.xlsx')
                      ->orWhere('original_name', 'like', '%.csv');
                });
            } elseif ($cat === 'image') {
                $query->where(function ($q) {
                    $q->where('original_name', 'like', '%.png')
                      ->orWhere('original_name', 'like', '%.jpg')
                      ->orWhere('original_name', 'like', '%.jpeg')
                      ->orWhere('original_name', 'like', '%.webp')
                      ->orWhere('original_name', 'like', '%.gif');
                });
            } elseif ($cat === 'archive') {
                $query->where(function ($q) {
                    $q->where('original_name', 'like', '%.zip')
                      ->orWhere('original_name', 'like', '%.rar')
                      ->orWhere('original_name', 'like', '%.7z');
                });
            }
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Calculate statistics
        $totalFilesCount = SharedFile::count();
        $totalStorageBytes = SharedFile::sum('file_size');
        $myFilesCount = SharedFile::where('user_id', auth()->id())->count();

        // Format total storage
        if ($totalStorageBytes >= 1073741824) {
            $formattedTotalStorage = number_format($totalStorageBytes / 1073741824, 2) . ' GB';
        } elseif ($totalStorageBytes >= 1048576) {
            $formattedTotalStorage = number_format($totalStorageBytes / 1048576, 2) . ' MB';
        } elseif ($totalStorageBytes >= 1024) {
            $formattedTotalStorage = number_format($totalStorageBytes / 1024, 1) . ' KB';
        } else {
            $formattedTotalStorage = $totalStorageBytes . ' B';
        }

        return view('shared_files.index', compact('files', 'totalFilesCount', 'formattedTotalStorage', 'myFilesCount'));
    }

    /**
     * Store a newly uploaded shared file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50 MB max limit
        ], [
            'file.required' => 'Silakan pilih berkas yang ingin diunggah.',
            'file.max' => 'Ukuran berkas melebihi batas maksimum 50 MB.',
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getClientMimeType();
        $fileSize = $uploadedFile->getSize();

        // Ensure storage directory exists
        $uploadDir = public_path('uploads/shared_files');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $fileName = time() . '_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move($uploadDir, $fileName);

        // Save record to DB
        SharedFile::create([
            'original_name' => $originalName,
            'file_path' => $fileName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('shared-files.index')->with('success', 'Berkas "' . $originalName . '" berhasil diunggah dan dibagikan!');
    }

    /**
     * Download the specified shared file.
     */
    public function download($id)
    {
        $file = SharedFile::findOrFail($id);
        $fullPath = public_path('uploads/shared_files/' . $file->file_path);

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Berkas fisik tidak ditemukan di server.');
        }

        return response()->download($fullPath, $file->original_name);
    }

    /**
     * Remove the specified shared file.
     */
    public function destroy($id)
    {
        $file = SharedFile::findOrFail($id);

        // Authorization check: owner or admin
        if (auth()->id() !== $file->user_id && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus berkas orang lain.');
        }

        $fullPath = public_path('uploads/shared_files/' . $file->file_path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $file->delete();

        return redirect()->route('shared-files.index')->with('success', 'Berkas "' . $file->original_name . '" berhasil dihapus.');
    }
}
