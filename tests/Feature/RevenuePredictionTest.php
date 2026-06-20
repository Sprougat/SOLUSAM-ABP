<?php

namespace Tests\Feature;

use CodeIgniter\Test\FeatureTestCase;
use Firebase\JWT\JWT;
use Config\Services;

class RevenuePredictionTest extends FeatureTestCase
{
    protected $baseURL = 'http://localhost:8080';

    public function setUp(): void
    {
        parent::setUp();
        $this->generateJWT();
    }

    protected function generateJWT()
    {
        $key = getenv('JWT_SECRET') ?: 'test_secret_key';
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'client_id' => 1,
        ];

        $this->token = JWT::encode($payload, $key, 'HS256');
    }

    public function testDashboardPrediction()
    {
        $response = $this->get('/api/v1/dashboard/prediction', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJSONFragment([
            'status' => true,
        ]);

        $data = $response->getJSON();
        $this->assertArrayHasKey('current_month_revenue', $data->data);
        $this->assertArrayHasKey('next_month_prediction', $data->data);
        $this->assertArrayHasKey('confidence', $data->data);
        $this->assertArrayHasKey('model', $data->data);
    }

    public function testDetailedPrediction()
    {
        $response = $this->get('/api/v1/laporan/prediction', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJSONFragment([
            'status' => true,
        ]);

        $data = $response->getJSON();
        $this->assertArrayHasKey('historical', $data->data);
        $this->assertArrayHasKey('predictions', $data->data);
        $this->assertArrayHasKey('data_points', $data->data);
        $this->assertArrayHasKey('confidence', $data->data);

        if (!empty($data->data->predictions)) {
            $this->assertCount(3, $data->data->predictions);
        }
    }

    public function testModelEvaluation()
    {
        $response = $this->get('/api/v1/laporan/evaluate-model', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJSONFragment([
            'status' => true,
        ]);

        $data = $response->getJSON();
        $this->assertArrayHasKey('mae', $data->data);
        $this->assertArrayHasKey('accuracy_level', $data->data);
        $this->assertArrayHasKey('training_data_points', $data->data);
        $this->assertArrayHasKey('test_data_points', $data->data);
    }

    public function testUnauthorizedAccess()
    {
        $response = $this->get('/api/v1/dashboard/prediction');

        $response->assertStatus(401);
    }

    public function testInvalidToken()
    {
        $response = $this->get('/api/v1/dashboard/prediction', [
            'headers' => [
                'Authorization' => 'Bearer invalid_token'
            ]
        ]);

        $response->assertStatus(401);
    }
}