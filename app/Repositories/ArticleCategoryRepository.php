<?php

namespace App\Repositories;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ArticleCategoryRepository extends Repository
{
    public function __construct()
    {
        $this->model = new ArticleCategory();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (array_key_exists('parent_id', $filters) && $filters['parent_id'] !== null) {
            $query->where('parent_id', (int) $filters['parent_id']);
        }

        if (!empty($filters['q'])) {
            $term = (string) $filters['q'];
            $query->where(function ($subQuery) use ($term) {
                $subQuery->where('name', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('name');
    }

    public function parentOptions(): Collection
    {
        $items = $this->model->newQuery()
            ->select('id', 'name', 'parent_id')
            ->withCount('children')
            ->orderBy('name')
            ->get();

        $items->each(function ($c) {
            $c->setAttribute('is_parent', (int) $c->children_count > 0);
            unset($c->children_count);
        });

        return $items;
    }

    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $data['name']);
        return parent::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        if (array_key_exists('slug', $data) && $data['slug'] !== null) {
            $currentCategory = $this->findOrFail($id);
            $base = $data['slug'] !== '' ? $data['slug'] : $currentCategory->name;
            $data['slug'] = $this->makeUniqueSlug($base, $currentCategory->id);
        }
        return parent::update($id, $data);
    }

    private function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $baseSlug = $slug;
        $suffix = 2;

        $slugExists = function (string $candidate) use ($ignoreId): bool {
            return ArticleCategory::query()
                ->when($ignoreId, fn($q) => $q->where('id', '<>', $ignoreId))
                ->where('slug', $candidate)
                ->exists();
        };

        while ($slugExists($slug)) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }


    public function getCategoryForEdit(int|string $id): ArticleCategory
    {
        $category = $this->model->newQuery()
            ->select('id', 'name', 'slug', 'parent_id')
            ->findOrFail($id);

        return $category;
    }

    protected function descendantIds(int|string $id): array
    {
        $rows = DB::select(
            'WITH RECURSIVE cte AS (
                SELECT id, parent_id FROM article_categories WHERE id = ?
                UNION ALL
                SELECT ac.id, ac.parent_id
                FROM article_categories ac
                INNER JOIN cte ON ac.parent_id = cte.id
            )
            SELECT id FROM cte WHERE id <> ?',
            [$id, $id]
        );

        return array_map(static fn($r) => (int) $r->id, $rows);
    }

    public function parentOptionsExcluding(int|string $currentId): EloquentCollection
    {
        $excludeIds = $this->descendantIds($currentId);
        $excludeIds[] = (int) $currentId;

        $items = $this->model->newQuery()
            ->select('id', 'name', 'parent_id')
            ->withCount('children')
            ->whereNotIn('id', $excludeIds)
            ->orderBy('name')
            ->get();

        $items->each(function ($c) {
            $c->setAttribute('is_parent', (int) $c->children_count > 0);
            unset($c->children_count);
        });

        return $items;
    }

    public function getById(int|string $id): array
    {
        $category = $this->getCategoryForEdit($id);
        $parentOptions = $this->parentOptionsExcluding($id);

        return [
            'category' => $category,
            'parentOptions' => $parentOptions,
        ];
    }
}
