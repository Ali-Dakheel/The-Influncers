<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class RegisterUser
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array{0: User, 1: string}
     */
    public function __invoke(array $attributes): array
    {
        [$user, $token] = DB::transaction(function () use ($attributes): array {
            $user = User::create($attributes);

            return [$user, $user->createToken('auth')->plainTextToken];
        });

        event(new Registered($user));

        return [$user, $token];
    }
}
