<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogPostStoreRequest;
use App\Http\Requests\BlogPostUpdateRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BlogTag;
use App\Services\BlogPublishService;
use App\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BlogPostController extends Controller
{
    public function __construct(
        private BlogPublishService $publishService,
        private SlugService $slugService
    ) {
        $this->middleware(['auth', 'verified']);
        $this->authorizeResource(Article::class, 'article');
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Article::class);
        $query = Article::with(['author', 'category'])->orderByDesc('created_at');
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        $posts = $query->paginate(15);
        $categories = ArticleCategory::orderBy('name')->get();
        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        Gate::authorize('create', Article::class);
        return view('admin.blog.posts.create', [
            'categories' => ArticleCategory::orderBy('name')->get(),
            'tags' => BlogTag::orderBy('name')->get(),
        ]);
    }

    public function store(BlogPostStoreRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        if (empty($data['slug'])) {
            $data['slug'] = $this->slugService->generate(new Article, $data['title']);
        }
        $article = Article::create($data);
        $article->tagsMany()->sync($data['tags'] ?? []);

        return redirect()->route('admin.blog.posts.index')->with('success', 'Article créé');
    }

    public function edit(Article $article)
    {
        Gate::authorize('update', $article);
        return view('admin.blog.posts.edit', [
            'post' => $article->load('tagsMany'),
            'categories' => ArticleCategory::orderBy('name')->get(),
            'tags' => BlogTag::orderBy('name')->get(),
        ]);
    }

    public function update(BlogPostUpdateRequest $request, Article $article)
    {
        Gate::authorize('update', $article);
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = $this->slugService->generate(new Article, $data['title']);
        }
        $article->update($data);
        $article->tagsMany()->sync($data['tags'] ?? []);

        return redirect()->route('admin.blog.posts.index')->with('success', 'Article mis à jour');
    }

    public function destroy(Article $article)
    {
        Gate::authorize('delete', $article);
        $article->delete();
        return back()->with('success', 'Article supprimé');
    }

    public function publish(Article $article)
    {
        Gate::authorize('publish', $article);
        $this->publishService->publish($article);
        return back()->with('success', 'Article publié');
    }

    public function unpublish(Article $article)
    {
        Gate::authorize('publish', $article);
        $this->publishService->unpublish($article);
        return back()->with('success', 'Article dépublié');
    }
}
