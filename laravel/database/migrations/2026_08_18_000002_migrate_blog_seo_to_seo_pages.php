<?php

use App\Models\BlogPost;
use App\Models\SeoPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        BlogPost::query()->withTrashed()->each(function (BlogPost $post) {
            $hasTitle = filled($post->getRawOriginal('meta_title'));
            $hasDesc = filled($post->getRawOriginal('meta_description'));

            if ($hasTitle || $hasDesc) {
                SeoPage::query()->updateOrCreate(
                    ['model_type' => BlogPost::class, 'model_id' => $post->id],
                    [
                        'title' => json_decode((string) $post->getRawOriginal('meta_title'), true),
                        'description' => json_decode((string) $post->getRawOriginal('meta_description'), true),
                        'robots' => 'index,follow',
                    ]
                );
            }
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->json('meta_title')->nullable()->after('published_at');
            $table->json('meta_description')->nullable()->after('meta_title');
        });
    }
};
