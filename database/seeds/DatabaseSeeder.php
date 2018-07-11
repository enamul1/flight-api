<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        // $this->call('UsersTableSeeder');
        $path = 'airport.sql';
        DB::unprepared(file_get_contents('airport.sql',FILE_USE_INCLUDE_PATH));
        $this->command->info('Airports table seeded!');
    }
}
