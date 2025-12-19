<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaUploadRequest;
use App\Models\MediaFile;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private MediaService $mediaService)
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(): View
    {
        $this->authorize('viewAny', MediaFile::class);

        $media = MediaFile::with('uploader')->latest()->paginate(20);

        return view('admin.blog.media.index', compact('media'));
    }

    public function store(MediaUploadRequest $request): RedirectResponse
    {
        $this->authorize('viewAny', MediaFile::class);

        $this->mediaService->upload($request->file('file'), [
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'uploader_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Média importé');
    }

    public function destroy(MediaFile $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        $this->mediaService->delete($media);

        return back()->with('success', 'Média supprimé');
    }
}
