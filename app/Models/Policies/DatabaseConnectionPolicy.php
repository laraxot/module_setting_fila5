<?php

declare(strict_types=1);

namespace Modules\Setting\Models\Policies;

use Modules\Setting\Models\DatabaseConnection;
use Modules\Xot\Contracts\ProfileContract;

/**
 * Autorizzazione per permesso: il modello in firma è il contratto Gate.
 *
 * @SuppressWarnings("PHPMD.UnusedFormalParameter")
 */
class DatabaseConnectionPolicy extends SettingBasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(ProfileContract $user): bool
    {
        return $user->hasPermissionTo('database_connection.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(ProfileContract $user, DatabaseConnection $databaseConnection): bool
    {
        return $user->hasPermissionTo('database_connection.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(ProfileContract $user): bool
    {
        return $user->hasPermissionTo('database_connection.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(ProfileContract $user, DatabaseConnection $databaseConnection): bool
    {
        return $user->hasPermissionTo('database_connection.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(ProfileContract $user, DatabaseConnection $databaseConnection): bool
    {
        return $user->hasPermissionTo('database_connection.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(ProfileContract $user, DatabaseConnection $databaseConnection): bool
    {
        return $user->hasPermissionTo('database_connection.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(ProfileContract $user, DatabaseConnection $databaseConnection): bool
    {
        return $user->hasPermissionTo('database_connection.forceDelete');
    }
}
