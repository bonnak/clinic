<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class UserAdministrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthorized_user_cannot_create_a_user()
    {
        $this->signIn();

        $user = factory(User::class)->make();

        $this->postJson('api/users', array_merge($user->toArray(), ['password' => 'secret']))
            ->assertStatus(403);

        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /** @test */
    public function authorized_user_can_create_a_user()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('create-users');
        $this->signIn($sign_in_user);

        $user = factory(User::class)->make();

        $this->postJson('api/users', array_merge($user->toArray(), ['password' => 'secret']))
            ->assertStatus(201);

        $this->assertDatabaseHas('users', $user->toArray());
    }

     /** @test */
    public function unauthorized_user_cannot_edit_a_user()
    {
        $this->signIn();

        $user = factory(User::class)->create();
        $user->email = 'edit@mail.com';

        $this->putJson("api/users/{$user->id}", $user->toArray())
            ->assertStatus(403);

        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /** @test */
    public function authorized_user_can_edit_a_user()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('edit-users');
        $this->signIn($sign_in_user);

        $user = factory(User::class)->create();
        $user->email = 'edit@mail.com';
        $user->active = true;

        $this->putJson("api/users/{$user->id}", $user->toArray())
            ->assertStatus(200);

        $this->assertDatabaseHas('users', $user->toArray());
    }

    /** @test */
    public function unauthorized_user_cannot_delete_a_user()
    {
        $this->signIn();

        $user = factory(User::class)->create();

        $this->deleteJson("api/users/{$user->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('users', $user->toArray());
        $this->assertNull($user->fresh()->deleted_at);
    }

    /** @test */
    public function authorized_user_can_delete_a_user()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('delete-users');
        $this->signIn($sign_in_user);

        $user = factory(User::class)->create();

        $this->deleteJson("api/users/{$user->id}")
            ->assertStatus(200);

        $this->assertDatabaseHas('users', $user->toArray());
        $this->assertNotNull($user->fresh()->deleted_at);
    }

    /** @test */
    public function username_cannot_change()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('edit-users');
        $this->signIn($sign_in_user);

        $user = factory(User::class)->create();
        $user->username = 'anotherusername';
        $user->email = 'edit@mail.com';

        $this->putJson("api/users/{$user->id}", $user->toArray());

        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /** @test */
    public function unauthorized_user_cannot_reset_a_user_password()
    {
        $this->signIn();

        $user = factory(User::class)->create(['password' => bcrypt('12345678')]);

        $this->putJson("api/users/{$user->id}/password/reset", [
            'password' => '87654321',
            'password_confirmation' => '87654321',
        ])->assertStatus(403);
    }

    /** @test */
    public function authorized_user_can_reset_a_user_password()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('reset-password-users');
        $this->signIn($sign_in_user);

        $user = factory(User::class)->create(['password' => bcrypt('12345678')]);

        $this->putJson("api/users/{$user->id}/password/reset", [
            'password' => '87654321',
            'password_confirmation' => '87654321',
        ])->assertStatus(200);
    }

    /** @test */
    public function authorize_user_can_fetch_users()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('view-users');
        $this->signIn($sign_in_user);

        factory(User::class, 2)->create();

        $this->getJson('api/users')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'username',
                        'email',
                    ],
                ],
            ]);
    }

    /** @test */
    public function unauthorize_user_cannot_fetch_users()
    {
        $sign_in_user = factory(User::class)->create();
        $this->signIn($sign_in_user);

        factory(User::class, 2)->create();

        $this->getJson('api/users')
            ->assertStatus(403)
            ->assertJsonMissing(['data']);
    }

    /** @test */
    public function authorize_user_can_view_a_user()
    {
        $sign_in_user = factory(User::class)->create();
        $sign_in_user->allow('view-users');
        $this->signIn($sign_in_user);

        $user = factory(User::class)->create(['id' => 10]);

        $response = $this->getJson("api/users/$user->id")
            ->assertStatus(200)
            ->assertJsonStructure([
                'username',
                'email',
            ]);
    }

    /** @test */
    public function unauthorize_user_cannot_view_a_user()
    {
        $sign_in_user = factory(User::class)->create();
        $this->signIn($sign_in_user);

        $user = factory(User::class)->create();

        $this->getJson("api/users/$user->id")
            ->assertStatus(403)
            ->assertJsonMissing([
                'username',
                'email',
            ]);
    }
}
