<?php

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class UsersRolePermisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        App\User::truncate();
        $user = factory(App\User::class)->create([
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
        ]);

        Bouncer::allow($user)->everything();
        Bouncer::ability()->firstOrCreate(['name' => 'views-users', 'title' => 'View users']);
        Bouncer::ability()->firstOrCreate(['name' => 'create-users', 'title' => 'Create users']);
        Bouncer::ability()->firstOrCreate(['name' => 'edit-users', 'title' => 'Edit users']);
        Bouncer::ability()->firstOrCreate(['name' => 'delete-users', 'title' => 'Delete users']);
        Bouncer::ability()->firstOrCreate(['name' => 'reset-password-users', 'title' => 'Reset users password']);

        Artisan::call('passport:client', ['--password' => true, '--no-interaction' => true]);

        $client = Client::first();
        $client->update(['user_id' => $user->id]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}