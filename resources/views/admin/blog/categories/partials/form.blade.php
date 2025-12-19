<div class="card p-3">
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug ?? '') }}">
        @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Parent (optionnel)</label>
        <select name="parent_id" class="form-select">
            <option value="">—</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id ?? null)==$parent->id)>{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>
</div>
