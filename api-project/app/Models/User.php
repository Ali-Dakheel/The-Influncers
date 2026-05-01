<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'country_id',
    'stripe_account_id',
    'stripe_onboarded',
    'sales_rep_id',
    'monthly_budget_cents',
    'income_target_cents',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'stripe_onboarded' => 'boolean',
            'monthly_budget_cents' => 'integer',
            'income_target_cents' => 'integer',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function isBrand(): bool
    {
        return $this->role === Role::Brand;
    }

    public function isInfluencer(): bool
    {
        return $this->role === Role::Influencer;
    }

    public function isAgency(): bool
    {
        return $this->role === Role::Agency;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class);
    }

    public function priceListItems(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function pricePackages(): HasMany
    {
        return $this->hasMany(PricePackage::class);
    }

    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'influencer_id');
    }

    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'brand_id');
    }

    public function salesRep(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function brandsRepresented(): HasMany
    {
        return $this->hasMany(User::class, 'sales_rep_id');
    }

    public function paymentsAsBrand(): HasMany
    {
        return $this->hasMany(Payment::class, 'brand_id');
    }

    public function paymentsAsInfluencer(): HasMany
    {
        return $this->hasMany(Payment::class, 'influencer_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'recipient_id');
    }
}
