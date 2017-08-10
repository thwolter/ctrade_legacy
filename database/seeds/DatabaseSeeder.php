<?php

use Illuminate\Database\Seeder;
use Backpack\Settings\database\seeds\SettingsTableSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CurrencySeeder::class);
        $this->call(TransactionTypeSeeder::class);
        $this->call(LimitTypeSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(RolesSeeder::class);

        $this->dev();
    }

    private function dev()
    {
        if (env('APP_ENV') == 'local') {

            $this->call(UserSeeder::class);

        }
    }
}
