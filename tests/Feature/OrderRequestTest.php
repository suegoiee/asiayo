<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Validator;

class OrderRequestTest extends TestCase
{
    private function validate(array $data)
    {
        $request = new OrderRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    #[Test]
    public function test_validation_for_valid_data()
    {
        $data = [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'price' => 1500,
            'currency' => 'TWD',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];
    
        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(200);
    }

    #[Test]
    public function test_price_is_over_2000()
    {
        $data = [
            'id' => 'A0000002',
            'name' => 'Melody Holiday Inn',
            'price' => 2500,
            'currency' => 'TWD',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(400);
        $response->assertJsonFragment(['price is over 2000']);
    }

    #[Test]
    public function test_currency_is_invalid()
    {
        $data = [
            'id' => 'A0000003',
            'name' => 'Melody Holiday Inn',
            'price' => 1500,
            'currency' => 'EUR',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(400);
        $response->assertJsonFragment(['currency format is wrong']);
    }

    #[Test]
    public function test_name_contains_non_english_characters()
    {
        $data = [
            'id' => 'A0000004',
            'name' => 'メロディ ホリデイイン',
            'price' => 1500,
            'currency' => 'TWD',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(400);
        $response->assertJsonFragment(['Name contains non-English characters']);
    }

    #[Test]
    public function test_name_is_not_capitalized()
    {
        $data = [
            'id' => 'A0000005',
            'name' => 'melody holiday inn',
            'price' => 1500,
            'currency' => 'TWD',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(400);
        $response->assertJsonFragment(['Name is not capitalized']);
    }

    #[Test]
    public function test_price_and_currency_for_usd_fail()
    {
        $data = [
            'id' => 'A0000005',
            'name' => 'Melody Holiday Inn',
            'price' => 150,
            'currency' => 'USD',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(400);
        $response->assertJsonFragment(['price is over 2000']);
    }

    #[Test]
    public function test_price_and_currency_for_usd_success()
    {
        $data = [
            'id' => 'A0000005',
            'name' => 'Melody Holiday Inn',
            'price' => 50,
            'currency' => 'USD',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ]
        ];
    
        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => 1550, 'currency' => 'TWD']);
    }
}
