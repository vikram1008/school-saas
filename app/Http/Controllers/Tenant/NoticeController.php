<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('publishedBy')
            ->latest()
            ->paginate(15);

        return view('tenant.notices.index', compact('notices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'visible_to' => ['required', 'in:all,parents,staff,students'],
        ]);

        Notice::create([
            'title'        => $request->title,
            'title_hi'     => $request->title_hi,
            'content'      => $request->content,
            'content_hi'   => $request->content_hi,
            'visible_to'   => $request->visible_to,
            'published_by' => Auth::guard('tenant')->id(),
            'published_at' => $request->boolean('is_published') ? now() : null,
            'expires_at'   => $request->expires_at,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('tenant.notices.index')
            ->with('success', 'Notice created successfully.');
    }

    public function update(Request $request, Notice $notice)
    {
        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'visible_to' => ['required', 'in:all,parents,staff,students'],
        ]);

        $notice->update([
            'title'        => $request->title,
            'title_hi'     => $request->title_hi,
            'content'      => $request->content,
            'content_hi'   => $request->content_hi,
            'visible_to'   => $request->visible_to,
            'expires_at'   => $request->expires_at,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') && !$notice->published_at
                ? now() : $notice->published_at,
        ]);

        return redirect()
            ->route('tenant.notices.index')
            ->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()
            ->route('tenant.notices.index')
            ->with('success', 'Notice deleted.');
    }
}