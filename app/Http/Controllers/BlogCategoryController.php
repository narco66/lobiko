<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\ArticleCategory;

class BlogCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->authorizeResource(ArticleCategory::class, 'category');
    }

    public function index()
    {
        $categories = ArticleCategory::orderBy('name')->paginate(15);
        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = ArticleCategory::orderBy('name')->get();
        return view('admin.blog.categories.create', compact('parents'));
    }

    public function store(CategoryStoreRequest $request)
    {
        ArticleCategory::create($request->validated());
        return redirect()->route('admin.blog.categories.index')->with('success', 'Catégorie créée');
    }

    public function edit(ArticleCategory $category)
    {
        $parents = ArticleCategory::where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.blog.categories.edit', compact('category', 'parents'));
    }

    public function update(CategoryUpdateRequest $request, ArticleCategory $category)
    {
        $category->update($request->validated());
        return redirect()->route('admin.blog.categories.index')->with('success', 'Catégorie mise à jour');
    }

    public function destroy(ArticleCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Catégorie supprimée');
    }
}
