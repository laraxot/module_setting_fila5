<?php

declare(strict_types=1);

namespace Modules\Setting\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Xot\Contracts\UserContract;

abstract class SettingBasePolicy
{
    use HandlesAuthorization;

    /**
     * Firma Laravel Gate: $ability è richiesto dal contratto, non usato qui.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function before(UserContract $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }
}
