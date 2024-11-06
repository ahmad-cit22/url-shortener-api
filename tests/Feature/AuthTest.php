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

    public function test_user_cannot_register_with_invalid_inputs()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'invalidEmail',
            'password' => 'Pa$$w0rd!',
            'password_confirmation' => 'Pa$$w0rd!'
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['email']
            ]);
    }

    public function test_user_cannot_register_with_duplicate_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Another User',
            'email' => 'test@example.com',
            'password' => 'Pa$$w0rd!',
            'password_confirmation' => 'Pa$$w0rd!'
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['email']
            ]);
    }

    public function test_user_cannot_register_with_password_confirmation_mismatch()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Pa$$w0rd!',
            'password_confirmation' => 'DifferentPassword!'
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['password']
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

        $response->assertJsonFragment(['status' => 'success']);
        $this->assertNotNull($response->json('data.token'));
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'invalid',
            'password' => 'invalid'
        ]);

        $response->assertUnauthorized()
            ->assertJsonStructure([
                'status',
                'message'
            ]);
    }

    public function test_user_cannot_login_with_missing_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertUnauthorized()
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['email', 'password']
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

    public function test_user_cannot_logout_without_token()
    {
        $response = $this->postJson('/api/v1/logout');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}
