<?php

namespace App\Http\Controllers;

use App\Models\TodoTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TodoTaskController extends Controller
{
    /**
     * Get or initialize today's tasks for the authenticated user.
     */
    public static function getOrInitializeTodayTasks($userId)
    {
        $today = Carbon::today()->toDateString();
        $tasks = TodoTask::where('user_id', $userId)
            ->where('task_date', $today)
            ->orderBy('time_start', 'asc')
            ->get();

        // If today is Monday-Friday (1-5) and no tasks exist, auto-initialize the schedule
        $dayOfWeek = Carbon::today()->dayOfWeek; // 0 (Sun) to 6 (Sat)
        $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;

        if ($tasks->isEmpty() && $isWeekday) {
            $defaultSchedule = [
                [
                    'title' => 'Filing prepare dokumen untuk scan Beksis',
                    'time_start' => '08:00',
                    'time_end' => '09:00',
                ],
                [
                    'title' => 'Draft dokumen Sales & Waste',
                    'time_start' => '09:00',
                    'time_end' => '10:00',
                ],
                [
                    'title' => 'Proses upload beksis',
                    'time_start' => '10:00',
                    'time_end' => '10:30',
                ],
                [
                    'title' => 'Lanjut proses draft dokumen Sales & Waste Jika ada tambahan',
                    'time_start' => '10:30',
                    'time_end' => '12:00',
                ],
                [
                    'title' => 'Proses draft dokumen Sales & Waste Jika ada tambahan',
                    'time_start' => '13:00',
                    'time_end' => '16:00',
                ],
                [
                    'title' => 'Print SPPB & BC25 / BC41 hari ini untuk prepare dokumen Beksis Besoknya',
                    'time_start' => '16:00',
                    'time_end' => '17:00',
                ]
            ];

            foreach ($defaultSchedule as $sched) {
                TodoTask::create([
                    'user_id' => $userId,
                    'title' => $sched['title'],
                    'time_start' => $sched['time_start'],
                    'time_end' => $sched['time_end'],
                    'status' => 'belum_mulai',
                    'task_date' => $today,
                ]);
            }

            // Retrieve again after creation
            $tasks = TodoTask::where('user_id', $userId)
                ->where('task_date', $today)
                ->orderBy('time_start', 'asc')
                ->get();
        }

        return $tasks;
    }

    /**
     * Display today's task list.
     */
    public function index()
    {
        $userId = auth()->id();
        $tasks = self::getOrInitializeTodayTasks($userId);
        
        // Calculate progress percentage
        $total = $tasks->count();
        $completed = $tasks->where('status', 'selesai')->count();
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        return view('excel.todo', compact('tasks', 'progress'));
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, $id)
    {
        $task = TodoTask::where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:belum_mulai,sedang_dikerjakan,menunggu,selesai,terlambat',
        ]);

        $task->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $task->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status tugas berhasil diperbarui!',
                'status' => $task->status,
                'notes' => $task->notes
            ]);
        }

        return redirect()->route('todo.index')->with('success', 'Status tugas berhasil diperbarui!');
    }

    /**
     * Store a custom task.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time_start' => 'required|string|max:5',
            'time_end' => 'required|string|max:5',
            'notes' => 'nullable|string',
        ]);

        TodoTask::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'status' => 'belum_mulai',
            'task_date' => Carbon::today()->toDateString(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('todo.index')->with('success', 'Tugas baru berhasil ditambahkan!');
    }

    /**
     * Delete a task.
     */
    public function destroy($id)
    {
        $task = TodoTask::where('user_id', auth()->id())->findOrFail($id);
        $task->delete();

        return redirect()->route('todo.index')->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Reset today's task list to default schedule.
     */
    public function resetToday()
    {
        TodoTask::where('user_id', auth()->id())
            ->where('task_date', Carbon::today()->toDateString())
            ->delete();

        return redirect()->route('todo.index')->with('success', 'Daftar tugas hari ini berhasil di-reset ke jadwal default.');
    }
}
