<?php

namespace App\Repositories;

use App\Models\Animal;
use Illuminate\Pagination\LengthAwarePaginator;

class AnimalRepository implements AnimalRepositoryInterface
{
    public function paginate(int $tutorId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Animal::with('coleira')
            ->where('tutor_id', $tutorId)
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['porte']  ?? null, fn ($q, $p) => $q->where('porte', $p))
            ->when($filters['busca']  ?? null, fn ($q, $b) => $q->where('nome', 'like', "%{$b}%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Animal
    {
        return Animal::with(['coleira.ultimaLocalizacao', 'tutor'])->find($id);
    }

    public function create(array $data): Animal
    {
        return Animal::create($data);
    }

    public function update(Animal $animal, array $data): bool
    {
        return $animal->update($data);
    }

    public function delete(Animal $animal): bool
    {
        return $animal->delete();
    }
}
