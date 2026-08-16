@extends('admin.layouts.app')

@section('title', __('AI Module'))

@section('breadcrumb')
    <span>{{ __('AI Module') }}</span>
@endsection

@section('content')
    @if (empty($activeProviders))
        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 mb-6 flex items-center justify-between">
            <span><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ __('No active AI providers configured yet. Set up OpenAI or OpenRouter to start using AI tools.') }}</span>
            <a href="{{ route('admin.ai.settings') }}" class="font-medium underline">{{ __('Go to Settings') }}</a>
        </div>
    @endif

    @php
        $features = [
            ['title' => __('SEO Enhancement'), 'desc' => __('Generate meta titles, descriptions and keywords for products, categories and pages.'), 'icon' => 'fa-magnifying-glass-chart', 'route' => route('admin.ai.seo')],
            ['title' => __('Blog Generator'), 'desc' => __('Draft full blog posts from a topic in seconds.'), 'icon' => 'fa-newspaper', 'route' => route('admin.ai.blog')],
            ['title' => __('Trending Research'), 'desc' => __('Discover trending products, niches and content ideas.'), 'icon' => 'fa-chart-line', 'route' => route('admin.ai.trending')],
            ['title' => __('Social Media Posts'), 'desc' => __('Create captions, hashtags and images for your campaigns.'), 'icon' => 'fa-share-nodes', 'route' => route('admin.ai.social')],
            ['title' => __('Product Descriptions'), 'desc' => __('Write persuasive product descriptions instantly.'), 'icon' => 'fa-file-pen', 'route' => route('admin.ai.product-description')],
            ['title' => __('AI Settings'), 'desc' => __('Manage providers, API keys and models.'), 'icon' => 'fa-gear', 'route' => route('admin.ai.settings')],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($features as $feature)
            <a href="{{ $feature['route'] }}" class="block bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:border-amber-400 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                    <i class="fa-solid {{ $feature['icon'] }} text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800 mb-1">{{ $feature['title'] }}</h3>
                <p class="text-xs text-slate-500">{{ $feature['desc'] }}</p>
            </a>
        @endforeach
    </div>
@endsection
