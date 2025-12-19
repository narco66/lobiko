<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;

class BlogPublishService
{
    public function publish(Article $article): Article
    {
        return $this->setStatus($article, 'published', now());
    }

    public function unpublish(Article $article): Article
    {
        return $this->setStatus($article, 'draft', null);
    }

    public function archive(Article $article): Article
    {
        return $this->setStatus($article, 'archived', $article->published_at);
    }

    public function schedule(Article $article, \DateTimeInterface $when): Article
    {
        return $this->setStatus($article, 'published', $when);
    }

    protected function setStatus(Article $article, string $status, $publishedAt): Article
    {
        return DB::transaction(function () use ($article, $status, $publishedAt) {
            $article->status = $status;
            $article->is_published = $status === 'published';
            $article->published_at = $publishedAt;
            $article->save();
            return $article->fresh();
        });
    }
}
