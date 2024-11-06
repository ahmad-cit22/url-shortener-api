<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Url;

class UrlV1Test extends TestCase
{
    public function test_user_can_shorten_url_v1()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(
                '/api/v1/shorten',
                [
                    'original_url' => 'https://example.com'
                ]
            );

        $response->assertCreated()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'original_url',
                    'short_url',
                    'visit_count',
                    'created_at'
                ],
                'message'
            ]);
    }

    public function test_user_can_list_urls_v1()
    {
        $user = User::factory()->create();
        Url::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/v1/urls');

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total',
                    'urls' => [
                        [
                            'original_url',
                            'short_url',
                            'visit_count',
                            'created_at'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    public function test_user_can_redirect_v1()
    {
        $url = Url::factory()->create([
            'short_url' => 'abcd12',
            'original_url' => 'https://example.com'
        ]);

        $response = $this->get('/v1/' . $url->short_url);

        $response->assertRedirect($url->original_url);
    }
}
