<?php

namespace App\Services;

use App\DTOs\ArticleCategoryCreateDTO;
use App\DTOs\ArticleCategoryUpdateDTO;
use App\Models\ArticleCategory;
use App\Repositories\ArticleCategoryRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ArticleCategoryService
{
    public function __construct(private ArticleCategoryRepository $repository) {}

    public function paginate(int $perPage, ?array $filters = null): LengthAwarePaginator
    {
        return $this->repository->listAll('name', 'ASC', $perPage, $filters ?? []);
    }

    public function parentOptions(): Collection
    {
        return $this->repository->parentOptions();
    }

    public function getById(int|string $id): array
    {
        return $this->repository->getById($id);
    }

    public function create(ArticleCategoryCreateDTO $dto): ArticleCategory
    {
        return $this->repository->create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'parent_id' => $dto->parent_id,
        ]);
    }

    public function updateById(int $id, ArticleCategoryUpdateDTO $dto): ArticleCategory
    {
        $this->repository->update($id, [
            'name' => $dto->name,
            'slug' => $dto->slug,
            'parent_id' => $dto->parent_id,
        ]);

        return $this->repository->findOrFail($id);
    }

    public function deleteById(int $id): void
    {
        $this->repository->delete($id);
    }
}
