<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BlogController extends Controller
{
    // TODO: No Post/Blog model or migration exists yet in this project.
    // These actions return empty data so the views render their designed
    // empty states until a blog module (model + migration + admin CRUD)
    // is introduced. The views are otherwise fully built out to the
    // mockup and only need real Eloquent data wired in here.

    public function index(): View
    {
        return view('website.blog.index', [
            'posts' => collect(),
            'categories' => [],
        ]);
    }

    public function show(string $slug): View
    {
        return view('website.blog.show', [
            'post' => null,
            'slug' => $slug,
            'relatedPosts' => collect(),
        ]);
    }
}
