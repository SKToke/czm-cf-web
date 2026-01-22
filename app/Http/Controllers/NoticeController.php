<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notice;
use Illuminate\Pagination\LengthAwarePaginator;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $query = Notice::query();
        $banner = Banner::getBannerFor('Announcement');

        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('published_date', '=', $request->month);
        }

        if ($request->has('year') && $request->year != '') {
            $query->whereYear('published_date', '=', $request->year);
        }

        $perPage = 10;
        $notices = $query->orderBy('published_date', 'desc')->paginate($perPage);

        foreach ($notices as $notice) {
            if ($notice->hasAttachments()) {
                foreach ($notice->attachments as $attachment) {
                    $filePath = '/admin/' . $attachment->file;
                    $fileExists = Storage::disk('public')->exists($filePath);
                    $attachment->fileExists = $fileExists;
                    $attachment->filePath = $fileExists ? Storage::disk('public')->url($filePath) : null;
                }
            }
        }

        if ($request->ajax()) {
            $htmlView = view('notice.filtered_notices', [
                'notices' => $notices,
            ])->render();

            return response()->json($htmlView);
        }

        return view('notice.index', ['notices' => $notices, 'banner' => $banner]);
    }
}
