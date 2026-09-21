<?php

declare(strict_types=1);

namespace Modules\Setting\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Xot\Contracts\ProfileContract;

abstract class SettingBasePolicy
{
    use HandlesAuthorization;

    public function before(ProfileContract $user, string $_ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }
}
