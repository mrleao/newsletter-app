<?php

namespace App\Services;

use App\DTOs\ArticleCreateDTO;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $resp = null;
        if ($dto->image) {
            $data['image_path'] = $dto->image->store('articles/images', 'r2');
        }

        if ($data['published_at'] === null && $data['status'] === 'published') {
            $data['published_at'] = now()->toDateTimeString();
        }

        DB::beginTransaction();
        try {
            $resp = $this->repository->create($data);
            DB::commit();
        } catch (\Throwable $e) {
            if ($data['image_path']) {
                Storage::disk('r2')->delete($data['image_path']);
            }
            DB::rollBack();
            throw $e;
        }

        return $resp;
    }

    public function update(int $id, ArticleCreateDTO $dto): Article
    {
        $article = $this->repository->findOrFail($id);

        $data = collect((array) $dto)
            ->except(['image', 'image_path'])
            ->all();

        $oldPath = $article->image_path;
        $newPath = null;

        if ($dto->image instanceof \Illuminate\Http\UploadedFile) {
            $newPath = $dto->image->store('articles/images', 'r2');
            $data['image_path'] = $newPath;
        }   

        if ($article->status == 'draft' && $article->published_at == null) {
            $data['published_at'] = now()->toDateTimeString();
        } else {
            $data['published_at'] = $article->published_at;
        }

        DB::beginTransaction();
        try {
            $this->repository->update($id, $data);
            DB::commit();
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('r2')->delete($newPath);
            }
            DB::rollBack();
            throw $e;
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('r2')->delete($oldPath);
            Storage::disk('public')->delete($oldPath);
        }

        return $article->refresh();
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
