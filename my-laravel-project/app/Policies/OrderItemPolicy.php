<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;

class OrderItemPolicy
{
    /**
     * Bepaal of de gebruiker het product mag terugbrengen.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OrderItem  $orderItem
     * @return bool
     */
    public function return(User $user, OrderItem $orderItem)
    {
        // De klant die het product heeft gehuurd mag het terugbrengen
        return $user->id === $orderItem->order->user_id;
    }
}
