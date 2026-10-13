<?php

namespace App\Http\Controllers;

use App\Models\CleaningArea;
use App\Models\CleaningLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CleaningController extends Controller
{
    public function index()
    {
        return Inertia::render('Cleaning/Index', [
            'areas' => CleaningArea::all(),
            'logs' => CleaningLog::with('cleaningArea')
                ->orderBy('scheduled_date', 'desc')
                ->get(),
        ]);
    }

    // เพิ่มโซนใหม่ (Admin)
    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        CleaningArea::create($validated);

        return back()->with('message', 'เพิ่มโซนสำเร็จ');
    }

    // สั่งงานบ้านใหม่ (Admin)
    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'cleaning_area_id' => 'required|exists:cleaning_areas,id',
            'title' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['status'] = 'PENDING';

        CleaningLog::create($validated);

        return back()->with('message', 'สั่งงานบ้านเรียบร้อย');
    }

    // อัปโหลดรูป Before / After และเปลี่ยนสถานะงาน (น้อง / พี่)
    public function updateStatus(Request $request, CleaningLog $log)
    {
        $request->validate([
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED',
            'before_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'after_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('before_image')) {
            $log->before_image = $request->file('before_image')->store('cleaning_photos', 'public');
        }

        if ($request->hasFile('after_image')) {
            $log->after_image = $request->file('after_image')->store('cleaning_photos', 'public');
        }

        $log->status = $request->status;
        if ($request->status === 'COMPLETED') {
            $log->completed_at = now();
        }

        $log->save();

        return back()->with('message', 'อัปเดตสถานะงานเรียบร้อย');
    }
}