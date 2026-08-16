<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->published()->with(['author', 'category'])->latest('published_at');

        if ($categorySlug = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $posts = $query->paginate(6)->withQueryString();

        $categories = BlogCategory::query()
            ->active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->having('posts_count', '>', 0)
            ->get()
            ->map(fn (BlogCategory $category) => [
                'slug' => $category->slug,
                'name' => $category->name,
                'count' => $category->posts_count,
            ]);

        return view('website.blog.index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function show(Request $request): View
    {
        $slug = $request->route('slug');

        $post = BlogPost::query()->published()->with(['author', 'category'])->where('slug', $slug)->first();

        $relatedPosts = collect();

        if ($post && $post->blog_category_id) {
            $relatedPosts = BlogPost::query()->published()
                ->where('blog_category_id', $post->blog_category_id)
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->limit(3)
                ->get();
        }

        return view('website.blog.show', [
            'post' => $post,
            'slug' => $slug,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
