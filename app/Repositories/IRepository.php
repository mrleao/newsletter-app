<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface IRepository
{
    public function find(int|string $id): ?Model;
    public function findOrFail(int|string $id): Model;

    public function listAll(
        string $orderBy = 'id',
        string $orderArgument = 'DESC',
        int $perPage = 30,
        array $filters = []
    ): LengthAwarePaginator;

    public function create(array $data): Model;
    public function update(int|string $id, array $data): bool;
    public function delete(int|string $id): bool;
}
