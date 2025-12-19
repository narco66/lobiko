<div class="row g-3">
    <div class="col-md-8">
        <div class="card p-3">
            <div class="mb-3">
                <label class="form-label">Titre</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required>
                @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug ?? '') }}">
                @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Résumé</label>
                <textarea name="excerpt" rows="3" class="form-control">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Contenu</label>
                <textarea name="content" rows="10" class="form-control" required>{{ old('content', $post->content ?? '') }}</textarea>
                @error('content')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <div class="mb-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select" required>
                    @foreach(['draft'=>'Brouillon','review'=>'Relecture','published'=>'Publié','archived'=>'Archivé'] as $key=>$label)
                        <option value="{{ $key }}" @selected(old('status', $post->status ?? 'draft')===$key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de publication</label>
                <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($post->published_at ?? null)->format('Y-m-d\\TH:i')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Catégorie</label>
                <select name="category_id" class="form-select">
                    <option value="">—</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id ?? '')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tags</label>
                <select name="tags[]" class="form-select" multiple>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" @selected(collect(old('tags', isset($post) ? $post->tagsMany->pluck('id')->all() : []))->contains($tag->id))>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Meta title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title ?? '') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta description</label>
                <textarea name="meta_description" rows="2" class="form-control">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
