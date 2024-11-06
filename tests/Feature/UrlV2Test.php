<?php

namespace Tests\Feature;

use App\Models\Url;
use App\Models\User;
use Tests\TestCase;

class UrlV2Test extends TestCase
{

    public function test_user_can_shorten_url_v2()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(
                '/api/v2/shorten',
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

    public function test_user_can_list_urls_v2()
    {
        $user = User::factory()->create();
        Url::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/v2/urls');

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

    public function test_user_can_redirect_and_count_visits_v2()
    {
        $url = Url::factory()->create([
            'short_url' => 'abcd12',
            'original_url' => 'https://example.com'
        ]);

        $response = $this->get('/v2/' . $url->short_url);

        $response->assertRedirect($url->original_url);

        $this->assertDatabaseHas('urls', [
            'id' => $url->id,
            'visit_count' => 1
        ]);
    }
}
