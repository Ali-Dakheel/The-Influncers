<?php

namespace App\Actions\Creator;

use App\Models\PricePackage;
use App\Models\User;

class CreatePricePackage
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(User $user, array $attributes): PricePackage
    {
        return PricePackage::create([
            ...$attributes,
            'user_id' => $user->id,
        ]);
    }
}
