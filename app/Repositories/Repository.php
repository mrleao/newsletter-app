<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Repository implements IRepository
{
    protected Model $model;
    protected const PAGINATE_QTTY = 30;

    public function find(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function listAll(
        string $orderBy = 'id',
        string $orderArgument = 'DESC',
        int $perPage = self::PAGINATE_QTTY,
        array $filters = []
    ): LengthAwarePaginator {
        $direction = strtolower($orderArgument) === 'asc' ? 'asc' : 'desc';
        $query = $this->model->newQuery();
        $query = $this->applyFilters($query, $filters);
        return $query->orderBy($orderBy, $direction)->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        $model = $this->findOrFail($id);
        return !empty($model) ? $model->update($data) : 0;
    }

    public function delete(int|string $id): bool
    {
        $model = $this->findOrFail($id);
        return (bool) $model->delete();
    }

    abstract protected function applyFilters(Builder $query, array $filters): Builder;
}
