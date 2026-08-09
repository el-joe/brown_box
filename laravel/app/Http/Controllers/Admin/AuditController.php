<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $viewer = $request->user('admin');

        $query = Activity::query()->with('causer')->latest();

        if (! $viewer->isSuperAdmin()) {
            $query->where('causer_type', Admin::class)->where('causer_id', $viewer->id);
        } elseif ($request->filled('admin_id')) {
            $query->where('causer_type', Admin::class)->where('causer_id', $request->integer('admin_id'));
        }

        if ($request->filled('model_type')) {
            $query->where('subject_type', $request->string('model_type')->toString());
        }

        if ($request->filled('action')) {
            $query->where('event', $request->string('action')->toString());
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $activities = $query->paginate(20)->withQueryString();

        return view('admin.audits.index', [
            'activities' => $activities,
            'admins' => $viewer->isSuperAdmin() ? Admin::query()->orderBy('name')->get() : collect(),
            'modelTypes' => Activity::query()->whereNotNull('subject_type')->distinct()->pluck('subject_type'),
            'filters' => $request->only(['admin_id', 'model_type', 'action', 'from', 'to']),
        ]);
    }
}
