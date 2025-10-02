<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ShortUrlController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ShortUrlControllerTest extends TestCase
{
    protected ShortUrlController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ShortUrlController();
    }

    public function test_store_returns_validation_error_for_invalid_url(): void
    {
        $request = Request::create('/api/urls', 'POST', [
            'url' => 'invalid-url',
        ]);

        $response = $this->controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Validation failed', $responseData['message']);
    }

    public function test_store_returns_validation_error_for_missing_url(): void
    {
        $request = Request::create('/api/urls', 'POST', []);

        $response = $this->controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Validation failed', $responseData['message']);
    }

    public function test_controller_methods_exist(): void
    {
        $this->assertTrue(method_exists($this->controller, 'index'));
        $this->assertTrue(method_exists($this->controller, 'store'));
        $this->assertTrue(method_exists($this->controller, 'show'));
        $this->assertTrue(method_exists($this->controller, 'update'));
        $this->assertTrue(method_exists($this->controller, 'destroy'));
        $this->assertTrue(method_exists($this->controller, 'redirect'));
    }

    public function test_controller_extends_base_controller(): void
    {
        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $this->controller);
    }
}
