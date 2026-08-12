<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\BlogPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    public function __construct(private readonly BlogPostService $blogPosts)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.blog.index', [
            'filters' => $request->only(['title', 'is_published', 'blog_category_id']),
            'categories' => BlogCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = BlogPost::query()->with(['author', 'category']);

        if ($title = $request->string('title')->toString()) {
            $query->where(function ($q) use ($title) {
                $q->whereRaw('LOWER(JSON_EXTRACT(title, "$.en")) LIKE ?', ['%'.mb_strtolower($title).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(title, "$.ar")) LIKE ?', ['%'.mb_strtolower($title).'%']);
            });
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        if ($request->filled('blog_category_id')) {
            $query->where('blog_category_id', $request->integer('blog_category_id'));
        }

        return DataTables::eloquent($query)
            ->addColumn('thumbnail', fn (BlogPost $post) => $post->thumbnail
                ? '<img src="'.e(asset_url($post->thumbnail)).'" class="w-10 h-10 rounded-lg object-cover border border-slate-200">'
                : '<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300"><i class="fa-solid fa-newspaper"></i></div>')
            ->addColumn('title_en', fn (BlogPost $post) => e($post->getTranslation('title', 'en')))
            ->addColumn('title_ar', fn (BlogPost $post) => e($post->getTranslation('title', 'ar')))
            ->addColumn('category_name', fn (BlogPost $post) => e($post->category?->getTranslation('name', 'en') ?? '—'))
            ->addColumn('published_at', fn (BlogPost $post) => $post->published_at?->format('Y-m-d') ?? '—')
            ->addColumn('status', fn (BlogPost $post) => view('admin.blog._status', ['post' => $post])->render())
            ->addColumn('actions', fn (BlogPost $post) => view('admin.blog._actions', ['post' => $post])->render())
            ->rawColumns(['thumbnail', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.blog.form', [
            'post' => new BlogPost(),
            'categories' => BlogCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $this->blogPosts->create($this->mapData($request));

        return redirect()->route('admin.blog.index')->with('success', __('Blog post created successfully.'));
    }

    public function edit(int $id): View
    {
        $post = $this->blogPosts->find($id);

        abort_if(! $post, 404);

        return view('admin.blog.form', [
            'post' => $post,
            'categories' => BlogCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(BlogPostRequest $request, int $id): RedirectResponse
    {
        $this->blogPosts->update($id, $this->mapData($request));

        return redirect()->route('admin.blog.index')->with('success', __('Blog post updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->blogPosts->delete($id);

        return redirect()->route('admin.blog.index')->with('success', __('Blog post deleted successfully.'));
    }

    public function validateBlogPost(BlogPostRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $post = $this->blogPosts->find($id);

        abort_if(! $post, 404);

        $post = $this->blogPosts->update($id, ['is_published' => ! $post->is_published]);

        return response()->json(['success' => true, 'is_active' => $post->is_published]);
    }

    private function mapData(BlogPostRequest $request): array
    {
        $data = $request->safe()->only(['title', 'content', 'excerpt', 'meta_title', 'meta_description', 'blog_category_id', 'published_at']);
        $data['is_published'] = $request->boolean('is_published');
        $data['author_id'] = auth('admin')->id();

        if ($request->boolean('is_published') && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('blog', 'public');
        }

        return $data;
    }
}
