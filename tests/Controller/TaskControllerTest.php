<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testCreateTaskReturnsJsonSuccess(): void
    {
        // 1. Create Symfony HTTP Client
        $client = self::createClient();

        // 2. Send a valid JSON request to app endpoint
        $client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Build authorization module',
                'budget' => '2500.00',
                'currency' => 'EUR'
            ])
        );

        // 3. Check for HTTP status of response (must be 201)
        $this->assertResponseStatusCodeSame(201);

        // 4. Check for JSON struct in response
        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('success', $responseContent['status']);
        $this->assertSame('Build authorization module', $responseContent['data']['title']);
        $this->assertSame('EUR', $responseContent['data']['currency']);

        $this->assertArrayHasKey('postgres_rate_snapshot', $responseContent['data']);
    }
}
