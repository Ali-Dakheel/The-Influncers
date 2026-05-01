<?php

namespace App\Actions\Creator;

use App\Models\Portfolio;
use App\Models\User;

class UpdatePortfolio
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(User $user, array $attributes): Portfolio
    {
        $portfolio = Portfolio::firstOrNew(['user_id' => $user->id]);
        $portfolio->fill($attributes)->save();

        return $portfolio->fresh();
    }
}
