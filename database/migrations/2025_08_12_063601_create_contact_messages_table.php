<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table des messages de contact
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->foreignUuid('replied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('email');
        });

        // Table des témoignages
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->integer('rating')->default(5);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'is_featured']);
        });

        // Table des catégories d'articles (doit précéder articles)
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
        });

        // Table des articles de blog
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->foreignUuid('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('article_categories')->onDelete('set null');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->json('tags')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('slug');
            $table->fullText(['title', 'excerpt', 'content']);
        });

        // Table des services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('full_description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('features')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('price_unit')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
        });

        // Table des statistiques
        Schema::create('statistiques', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->bigInteger('value')->default(0);
            $table->string('unit')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('key');
        });

        // Table des FAQ
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('question');
            $table->text('answer');
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_published']);
        });

        // Table des partenaires
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['payment', 'insurance', 'medical', 'logistics', 'technology', 'other']);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        // Table des newsletters
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->boolean('is_subscribed')->default(true);
            $table->string('token')->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['is_subscribed', 'confirmed_at']);
        });

        // Table des pages personnalisées
        Schema::create('custom_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('template')->default('default');
            $table->json('meta_data')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('in_menu')->default(false);
            $table->integer('menu_order')->default(0);
            $table->timestamps();

            $table->index(['slug', 'is_published']);
        });

        // Table des bannières promotionnelles
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('button_text')->nullable();
            $table->enum('position', ['home_top', 'home_middle', 'home_bottom', 'sidebar', 'popup']);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('click_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();

            $table->index(['position', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });

        // Table des équipes
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('social_links')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // Table des offres d'emploi
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department');
            $table->string('location');
            $table->enum('type', ['full_time', 'part_time', 'contract', 'internship', 'freelance']);
            $table->enum('level', ['junior', 'mid', 'senior', 'lead', 'executive']);
            $table->text('description');
            $table->text('requirements');
            $table->text('benefits')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 5)->default('XAF');
            $table->boolean('is_remote')->default(false);
            $table->boolean('is_active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->integer('applications_count')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'expires_at']);
            $table->index('slug');
        });

        // Table des candidatures
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('cv_file');
            $table->string('cover_letter_file')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'reviewing', 'shortlisted', 'interviewed', 'accepted', 'rejected'])->default('new');
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['job_offer_id', 'status']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_offers');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('custom_pages');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('statistiques');
        Schema::dropIfExists('services');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_categories');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('contact_messages');
    }
};
