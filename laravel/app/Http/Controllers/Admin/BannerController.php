<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    public function __construct(private readonly BannerService $banners)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.banners.index', [
            'filters' => $request->only(['title', 'type', 'is_active']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Banner::query();

        if ($title = $request->string('title')->toString()) {
            $query->where(function ($q) use ($title) {
                $q->whereRaw('LOWER(JSON_EXTRACT(title, "$.en")) LIKE ?', ['%'.mb_strtolower($title).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(title, "$.ar")) LIKE ?', ['%'.mb_strtolower($title).'%']);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return DataTables::eloquent($query->orderBy('sort_order'))
            ->addColumn('image', fn (Banner $banner) => '<img src="'.e(asset_url($banner->image)).'" class="w-16 h-10 rounded-lg object-cover border border-slate-200">')
            ->addColumn('title_en', fn (Banner $banner) => e($banner->getTranslation('title', 'en')))
            ->addColumn('title_ar', fn (Banner $banner) => e($banner->getTranslation('title', 'ar')))
            ->addColumn('type', fn (Banner $banner) => e(ucfirst($banner->type)))
            ->addColumn('status', fn (Banner $banner) => view('admin.banners._status', ['banner' => $banner])->render())
            ->addColumn('actions', fn (Banner $banner) => view('admin.banners._actions', ['banner' => $banner])->render())
            ->rawColumns(['image', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.banners.form', [
            'banner' => new Banner(),
        ]);
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $this->banners->create($this->mapData($request));

        return redirect()->route('admin.banners.index')->with('success', __('Banner created successfully.'));
    }

    public function edit(int $id): View
    {
        $banner = $this->banners->find($id);

        abort_if(! $banner, 404);

        return view('admin.banners.form', [
            'banner' => $banner,
        ]);
    }

    public function update(BannerRequest $request, int $id): RedirectResponse
    {
        $this->banners->update($id, $this->mapData($request));

        return redirect()->route('admin.banners.index')->with('success', __('Banner updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->banners->delete($id);

        return redirect()->route('admin.banners.index')->with('success', __('Banner deleted successfully.'));
    }

    public function validateBanner(BannerRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $banner = $this->banners->find($id);

        abort_if(! $banner, 404);

        $banner = $this->banners->update($id, ['is_active' => ! $banner->is_active]);

        return response()->json(['success' => true, 'is_active' => $banner->is_active]);
    }

    private function mapData(BannerRequest $request): array
    {
        $data = $request->safe()->only(['title', 'type', 'url']);
        $data['sort_order'] = (int) $request->input('sort_order', 0);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        return $data;
    }
}
