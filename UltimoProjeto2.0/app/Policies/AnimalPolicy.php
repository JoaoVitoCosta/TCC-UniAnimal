<?php

namespace App\Policies;

use App\Models\Animal;
use App\Models\User;

class AnimalPolicy
{
    /**
     * Listagem e criação: liberado para qualquer usuário autenticado que
     * já possua um perfil de tutor (o escopo por tutor é aplicado na query).
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->tutor;
    }

    public function create(User $user): bool
    {
        return (bool) $user->tutor;
    }

    /**
     * Um usuário só pode ver/editar/remover animais do seu próprio tutor.
     */
    public function view(User $user, Animal $animal): bool
    {
        return $user->tutor && $animal->tutor_id === $user->tutor->id;
    }

    public function update(User $user, Animal $animal): bool
    {
        return $this->view($user, $animal);
    }

    public function delete(User $user, Animal $animal): bool
    {
        return $this->view($user, $animal);
    }
}
