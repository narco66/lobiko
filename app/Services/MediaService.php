<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function upload(UploadedFile $file, array $meta = []): MediaFile
    {
        $path = $file->store('media', $meta['disk'] ?? null);
        return DB::transaction(function () use ($file, $path, $meta) {
            return MediaFile::create([
                'path' => $path,
                'disk' => $meta['disk'] ?? config('filesystems.default'),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
                'alt_text' => $meta['alt_text'] ?? null,
                'caption' => $meta['caption'] ?? null,
                'uploader_id' => $meta['uploader_id'] ?? null,
            ]);
        });
    }

    public function delete(MediaFile $media): void
    {
        DB::transaction(function () use ($media) {
            Storage::disk($media->disk)->delete($media->path);
            $media->delete();
        });
    }
}
