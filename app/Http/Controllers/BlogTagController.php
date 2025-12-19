<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagStoreRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Models\BlogTag;

class BlogTagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->authorizeResource(BlogTag::class, 'tag');
    }

    public function index()
    {
        $tags = BlogTag::orderBy('name')->paginate(15);
        return view('admin.blog.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.blog.tags.create');
    }

    public function store(TagStoreRequest $request)
    {
        BlogTag::create($request->validated());
        return redirect()->route('admin.blog.tags.index')->with('success', 'Tag créé');
    }

    public function edit(BlogTag $tag)
    {
        return view('admin.blog.tags.edit', compact('tag'));
    }

    public function update(TagUpdateRequest $request, BlogTag $tag)
    {
        $tag->update($request->validated());
        return redirect()->route('admin.blog.tags.index')->with('success', 'Tag mis à jour');
    }

    public function destroy(BlogTag $tag)
    {
        $tag->delete();
        return back()->with('success', 'Tag supprimé');
    }
}
