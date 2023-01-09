<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;


class PasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     *
     */
    public function setUp() :void {
        parent::setUp();
        $this->seedDatabase();
    }

    /**
     * Successful password Token Creation request
     */
    public function testPasswordCreateTokenSuccess() {
        //Notification::fake();

        $response = $this->actingAs($this->superAdmin, 'api')->postJson('api/passwords/create', [
            'email' => $this->user->email
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => __('validation.reset_token.success')
        ]);
    }
    /**
     * Successful password Token Creation request
     */
    public function testPasswordCreateTokenInvalidEmail() {
        //Notification::fake();

        $response = $this->actingAs($this->superAdmin, 'api')->postJson('api/passwords/create', [
            'email' => "john@doe.com"
        ]);
        dd($response);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => __('validation.reset_token.success')
        ]);
    }
}
