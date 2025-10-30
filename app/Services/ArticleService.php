<?php

namespace App\Services;

use App\DTOs\ArticleCreateDTO;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;

final class ArticleService
{
    public function __construct(private ArticleRepository $repository) {}

    public function paginate(int $perPage, ?array $filters = null): LengthAwarePaginator
    {
        return $this->repository->listAll('published_at', 'DESC', $perPage, $filters ?? []);
    }


    public function create(ArticleCreateDTO $dto): Article
    {
        $data = (array) $dto;
        if ($dto->image) {
            $data['image_path'] = $dto->image->store('articles/images', 'public');
        }

        if ($data['published_at'] === null && $data['status'] === 'published') {
            $data['published_at'] = now()->toDateTimeString();
        }

        return $this->repository->create($data);
    }

    public function update(int $id, ArticleCreateDTO $dto): Article
    {
        $data = (array) $dto;
        if ($dto->image) {
            $data['image_path'] = $dto->image->store('articles/images', 'public');
        }

        if ($data['published_at'] === null && $data['status'] === 'published') {
            $data['published_at'] = now()->toDateTimeString();
        }
        $this->repository->update($id, $data);
        return $this->repository->findOrFail($id);
    }

    public function deleteById(int $id): void
    {
        $this->repository->delete($id);
    }

    public function find(int $id): ?Article
    {
        return $this->repository->find($id);
    }
}
