<?php

namespace App\Http\Controllers;

use App\Models\EximDocument;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function dashboard()
    {
        // Fetch pending documents first to trigger retrieved boot logic (auto-updates status to 'Perlu Follow Up' if > 7 days)
        EximDocument::where('status', '!=', 'Sudah Kembali')->get();

        // Calculate statistics
        $total_aktif = EximDocument::where('status', '!=', 'Sudah Kembali')->count();
        $menunggu_tanda_tangan = EximDocument::where('status', 'Menunggu Tanda Tangan')->count();
        $sudah_kembali = EximDocument::where('status', 'Sudah Kembali')->count();
        $lewat_deadline = EximDocument::where('status', 'Perlu Follow Up')->count();

        // Average signing time in days
        $returnedDocs = EximDocument::where('status', 'Sudah Kembali')->whereNotNull('tgl_kembali')->get();
        $rata_rata_hari = 0;
        if ($returnedDocs->count() > 0) {
            $totalDays = $returnedDocs->sum(function ($doc) {
                return $doc->days_pending;
            });
            $rata_rata_hari = round($totalDays / $returnedDocs->count(), 1);
        }

        // Longest pending return
        $dokumen_terlama = EximDocument::where('status', '!=', 'Sudah Kembali')
            ->orderBy('tgl_diserahkan', 'asc')
            ->first();

        // AI Assistant warnings for overdue documents
        $overdueDocs = EximDocument::where('status', 'Perlu Follow Up')
            ->orderBy('tgl_diserahkan', 'asc')
            ->get();

        $ai_warnings = [];
        foreach ($overdueDocs as $doc) {
            $days = $doc->days_pending;
            $ai_warnings[] = [
                'id' => $doc->id,
                'no_aju' => $doc->no_aju,
                'pic' => $doc->pic,
                'days' => $days,
                'message' => "Peringatan: Dokumen No. AJU {$doc->no_aju} (PIC: {$doc->pic}) belum kembali selama {$days} hari. Segera lakukan follow up ke supervisor!",
                'wa_link' => "https://wa.me/?text=" . urlencode("Halo {$doc->pic}, mohon dibantu untuk memfollow-up dokumen EXIM No. AJU: {$doc->no_aju} yang diserahkan sejak tanggal " . $doc->tgl_diserahkan->format('d-m-Y') . " karena sudah melewati batas 7 hari tanda tangan supervisor. Terima kasih!")
            ];
        }

        // Recent documents for quick view
        $recent_documents = EximDocument::orderBy('updated_at', 'desc')->take(5)->get();

        // Fetch today's tasks & progress
        $userId = auth()->id();
        $todoTasks = \App\Http\Controllers\TodoTaskController::getOrInitializeTodayTasks($userId);
        $totalTodo = $todoTasks->count();
        $completedTodo = $todoTasks->where('status', 'selesai')->count();
        $todoProgress = $totalTodo > 0 ? round(($completedTodo / $totalTodo) * 100) : 0;

        return view('dashboard', compact(
            'total_aktif',
            'menunggu_tanda_tangan',
            'sudah_kembali',
            'lewat_deadline',
            'rata_rata_hari',
            'dokumen_terlama',
            'ai_warnings',
            'recent_documents',
            'todoTasks',
            'todoProgress'
        ));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Trigger status updates
        EximDocument::where('status', '!=', 'Sudah Kembali')->get();

        $query = EximDocument::query();

        // Search by no_aju or pic
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('no_aju', 'like', '%' . $request->search . '%')
                  ->orWhere('pic', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $documents = $query->orderBy('tgl_diserahkan', 'desc')->paginate(10)->withQueryString();

        return view('documents.index', compact('documents'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return redirect()->route('documents.edit', $id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_aju' => 'required|string|unique:exim_documents,no_aju',
            'pic' => 'required|string|max:255',
            'tgl_diserahkan' => 'required|date',
            'tgl_kembali' => 'nullable|date|after_or_equal:tgl_diserahkan',
            'catatan' => 'nullable|string',
        ]);

        $status = 'Menunggu Tanda Tangan';
        if ($request->filled('tgl_kembali')) {
            $status = 'Sudah Kembali';
        } else {
            $days = Carbon::parse($request->tgl_diserahkan)->startOfDay()->diffInDays(now()->startOfDay());
            if ($days >= 7) {
                $status = 'Perlu Follow Up';
            }
        }

        EximDocument::create([
            'no_aju' => $request->no_aju,
            'pic' => $request->pic,
            'tgl_diserahkan' => $request->tgl_diserahkan,
            'tgl_kembali' => $request->tgl_kembali,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $document = EximDocument::findOrFail($id);
        return view('documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $document = EximDocument::findOrFail($id);

        $request->validate([
            'no_aju' => 'required|string|unique:exim_documents,no_aju,' . $id,
            'pic' => 'required|string|max:255',
            'tgl_diserahkan' => 'required|date',
            'tgl_kembali' => 'nullable|date|after_or_equal:tgl_diserahkan',
            'catatan' => 'nullable|string',
        ]);

        $status = 'Menunggu Tanda Tangan';
        if ($request->filled('tgl_kembali')) {
            $status = 'Sudah Kembali';
        } else {
            $days = Carbon::parse($request->tgl_diserahkan)->startOfDay()->diffInDays(now()->startOfDay());
            if ($days >= 7) {
                $status = 'Perlu Follow Up';
            }
        }

        $document->update([
            'no_aju' => $request->no_aju,
            'pic' => $request->pic,
            'tgl_diserahkan' => $request->tgl_diserahkan,
            'tgl_kembali' => $request->tgl_kembali,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $document = EximDocument::findOrFail($id);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus!');
    }
}
