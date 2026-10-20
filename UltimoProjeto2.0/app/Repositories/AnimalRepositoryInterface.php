<?php

namespace App\Repositories;

use App\Models\Animal;
use Illuminate\Pagination\LengthAwarePaginator;

interface AnimalRepositoryInterface
{
    public function paginate(int $tutorId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Animal;
    public function create(array $data): Animal;
    public function update(Animal $animal, array $data): bool;
    public function delete(Animal $animal): bool;
}
