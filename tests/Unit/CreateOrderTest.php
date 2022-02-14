<?php

namespace Tests\Unit;

use Tests\TestCase;
use Faker\Factory;
use Faker\Generator;
class CreateOrderTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $access_token;

    public function testOrder()
    {
        $payload = [
            'email' => 'ralph.santos@xurpas.com',
            'password' =>'test123'
        ];
        $response = $this->json('post','/api/login',$payload);
        $token = $response->decodeResponseJson()['access_token'];

        $this->access_token = $token;

        $this->create_order_success_test();
        $this->create_order_fail_test();
        $this->assertTrue(true);
    }

    public function create_order_success_test(){
        $this->withoutExceptionHandling();
        $orderPayload = [
            "order" => [
                [
                    "product_id" => "1",
                    "quantity" => "5"
                ]
            ]
        ];
        $response = $this->withHeader('Authorization','Bearer '.$this->access_token)
                        ->json('post','/api/order',$orderPayload);
        $response->assertStatus(200)->assertJsonStructure([
            'message'
        ]);
    }

    public function create_order_fail_test(){
        $this->withoutExceptionHandling();
        $orderPayload = [
            "order" => [
                [
                    "product_id" => "2",
                    "quantity" => "999"
                ]
            ]
        ];
        $response = $this->withHeader('Authorization','Bearer '.$this->access_token)
                        ->json('post','/api/order',$orderPayload);

        $response->assertStatus(400)->assertJsonStructure([
            'message'
        ]);
    }
}
