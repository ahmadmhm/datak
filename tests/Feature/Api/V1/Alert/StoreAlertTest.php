<?php

namespace Tests\Feature\Api\V1\Alert;

use App\Repositories\AlertRepository;
use Illuminate\Http\Response;
use Tests\TestCase;

class StoreAlertTest extends TestCase
{
    protected string $url = '/api/v1/alerts';

    protected string $index = 'alerts';

    public function ttest_that_user_can_store_alert()
    {
        $data = [
            'name' => fake()->name,
        ];
        $responseData = [
            'message' => 'You have stored successfully',
        ];

        $this->postJson($this->url, $data)
            ->assertJson($responseData)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_that_user_can_not_store_duplicate_alert()
    {
        $data = [
            'name' => fake()->name,
        ];
        app(AlertRepository::class)
            ->storeUserAlert(config('index.alert.default_user'), $data['name']);
        $responseData = [
            'message' => 'You have stored this resource before',
        ];

        $this->postJson($this->url, $data)
            ->assertJson($responseData)
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
