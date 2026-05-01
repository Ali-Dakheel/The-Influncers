<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['code' => 'US', 'name' => 'United States', 'default_locale' => 'en', 'currency' => 'USD'],
            ['code' => 'GB', 'name' => 'United Kingdom', 'default_locale' => 'en', 'currency' => 'GBP'],
            ['code' => 'SE', 'name' => 'Sweden', 'default_locale' => 'sv', 'currency' => 'SEK'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'default_locale' => 'en', 'currency' => 'AED'],
            ['code' => 'BH', 'name' => 'Bahrain', 'default_locale' => 'en', 'currency' => 'BHD'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['code' => $country['code']], $country);
        }
    }
}
