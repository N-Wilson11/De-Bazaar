<?php

namespace App\Policies;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertisementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the advertisement.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advertisement  $advertisement
     * @return bool
     */
    public function manage(User $user, Advertisement $advertisement)
    {
        return $user->id === $advertisement->user_id;
    }
}
