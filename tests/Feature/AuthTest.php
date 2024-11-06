<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Pa$$w0rd!',
            'password_confirmation' => 'Pa$$w0rd!'
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'name', 'email', 'created_at'],
                'message'
            ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => ['token', 'user'],
                'message'
            ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/logout');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Logout successful.'
            ]);
    }
}
