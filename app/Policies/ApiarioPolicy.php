<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Apiario;

class ApiarioPolicy
{
    public function view(User $user, Apiario $apiario): bool    { return $user->id === $apiario->user_id || $user->hasRole('admin'); }
    public function create(User $user): bool                    { return true; } // o según plan
    public function update(User $user, Apiario $apiario): bool  { return $user->id === $apiario->user_id || $user->hasRole('admin'); }
    public function delete(User $user, Apiario $apiario): bool  { return $user->id === $apiario->user_id || $user->hasRole('admin'); }
    public function restore(User $user, Apiario $apiario): bool { return $this->delete($user, $apiario); }
}
