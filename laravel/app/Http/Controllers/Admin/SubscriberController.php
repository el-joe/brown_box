<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SubscriberController extends Controller
{
    public function index(): View
    {
        return view('admin.subscribers.index', [
            'activeCount' => Subscriber::query()->active()->count(),
            'totalCount' => Subscriber::query()->count(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Subscriber::query();

        if ($search = trim((string) $request->input('search.value'))) {
            $query->where('email', 'like', "%{$search}%");
        }

        return DataTables::eloquent($query)
            ->addColumn('status', fn (Subscriber $subscriber) => $subscriber->is_active
                ? '<span class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">'.__('Active').'</span>'
                : '<span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">'.__('Unsubscribed').'</span>')
            ->addColumn('subscribed_at', fn (Subscriber $subscriber) => $subscriber->created_at->format('Y-m-d H:i'))
            ->addColumn('actions', fn (Subscriber $subscriber) => view('admin.subscribers._actions', ['subscriber' => $subscriber])->render())
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')->with('success', __('Subscriber removed successfully.'));
    }

    public function compose(): View
    {
        return view('admin.subscribers.compose', [
            'activeCount' => Subscriber::query()->active()->count(),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $subscribers = Subscriber::query()->active()->pluck('email');

        foreach ($subscribers as $email) {
            Mail::to($email)->queue(new NewsletterMail($data['subject'], $data['body']));
        }

        return redirect()->route('admin.subscribers.compose')
            ->with('success', __('Newsletter queued for :count subscriber(s).', ['count' => $subscribers->count()]));
    }
}
