<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class ArticleRepository extends Repository
{
    public function __construct()
    {
        $this->model = new Article();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', (string) $filters['status']);
        }

        if (!empty($filters['q'])) {
            $term = (string) $filters['q'];
            $query->where(function ($subQuery) use ($term) {
                $subQuery->where('title', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $data['title']);
        return parent::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        if (array_key_exists('slug', $data) && $data['slug'] !== null) {
            $currentArticle = $this->findOrFail($id);
            $base = $data['slug'] !== '' ? $data['slug'] : $currentArticle->title;
            $data['slug'] = $this->makeUniqueSlug($base, $currentArticle->id);
        }
        return parent::update($id, $data);
    }

    private function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $baseSlug = $slug;
        $suffix = 2;

        $slugExists = function (string $candidate) use ($ignoreId): bool {
            return Article::query()
                ->when($ignoreId, fn($q) => $q->where('id', '<>', $ignoreId))
                ->where('slug', $candidate)
                ->exists();
        };

        while ($slugExists($slug)) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }

    
}
