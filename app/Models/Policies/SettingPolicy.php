<?php

declare(strict_types=1);

namespace Modules\Setting\Models\Policies;

use Modules\Setting\Models\Setting;
use Modules\Xot\Contracts\ProfileContract;

/**
 * Autorizzazione per permesso: il modello in firma è il contratto Gate.
 *
 * @SuppressWarnings("PHPMD.UnusedFormalParameter")
 */
class SettingPolicy extends SettingBasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(ProfileContract $user): bool
    {
        return $user->hasPermissionTo('setting.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(ProfileContract $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('setting.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(ProfileContract $user): bool
    {
        return $user->hasPermissionTo('setting.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(ProfileContract $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('setting.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(ProfileContract $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('setting.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(ProfileContract $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('setting.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(ProfileContract $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('setting.forceDelete');
    }
}
