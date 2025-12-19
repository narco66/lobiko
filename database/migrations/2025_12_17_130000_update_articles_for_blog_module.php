<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('article_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('article_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('article_categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'status')) {
                $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft')->after('content');
            }
            if (!Schema::hasColumn('articles', 'read_time')) {
                $table->unsignedSmallInteger('read_time')->nullable()->after('status');
            }
            if (!Schema::hasColumn('articles', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('meta_data');
                $table->string('meta_description', 300)->nullable()->after('meta_title');
                $table->string('canonical_url')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('articles', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (!Schema::hasTable('blog_tags')) {
            Schema::create('blog_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->index('slug');
            });
        }

        if (!Schema::hasTable('article_tag')) {
            Schema::create('article_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained('blog_tags')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['article_id', 'tag_id']);
            });
        }

        if (!Schema::hasTable('media_files')) {
            Schema::create('media_files', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('path');
                $table->string('disk')->default(config('filesystems.default'));
                $table->string('mime')->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->string('original_name')->nullable();
                $table->string('alt_text')->nullable();
                $table->string('caption')->nullable();
                $table->foreignUuid('uploader_id')->nullable()->constrained('users')->nullOnDelete();
                $table->nullableMorphs('mediable');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('blog_tags');
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'status')) {
                $table->dropColumn(['status', 'read_time', 'meta_title', 'meta_description', 'canonical_url', 'deleted_at']);
            }
        });
        Schema::table('article_categories', function (Blueprint $table) {
            if (Schema::hasColumn('article_categories', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
            if (Schema::hasColumn('article_categories', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
