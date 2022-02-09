<?php

namespace Tests\Unit;

use App\User;
use Faker\Factory;
use Faker\Generator;
use Tests\TestCase;
use Hash;
use Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBasicTest(){
        $this->user_registration_api_request();
        $this->user_registration_failed_api_request();
        $this->user_login_api_request();
        $this->user_login_failed_api_request();
    }
     

    public function user_registration_api_request()
    {
        $this->withoutExceptionHandling();
        $faker = Factory::create();
        $payload = [
            'name' => $faker->name(),
            'email'  => $faker->email(),
            'password' => $faker->password()
        ];
        $response = $this->json('post','/api/register',$payload);
        $response->assertJsonStructure([
            'message'
        ]);
        $response->assertStatus(201);
    }

    public function user_registration_failed_api_request()
    {
        $this->withoutExceptionHandling();
        $faker = Factory::create();
        $user = User::first();
        $payload = [
            'name' => $faker->name(),
            'email'  => $user->email,
            'password' => "test123"
        ];
        $response = $this->json('post','/api/register',$payload);
        $response->assertJsonStructure([
            'message'
        ]);
        $response->assertStatus(400);
    }

    public function user_login_api_request()
    {
        $this->withoutExceptionHandling();
        $payload = [
            'email'  =>'ralph.santos@xurpas.com',
            'password' => 'test123'
        ];
        $response = $this->json('post','/api/login',$payload);
        $response->assertStatus(201)->assertJsonStructure([
            'access_token'
        ]);
    }

    public function user_login_failed_api_request()
    {
        $this->withoutExceptionHandling();
        $faker = Factory::create();
        $payload = [
            'name' => $faker->name(),
            'email'  => $faker->email(),
            'password' => $faker->password()
        ];
        $response = $this->json('post','/api/login',$payload);
        $response->assertStatus(401)->assertJsonStructure([
            'message'
        ]);
    }
}
