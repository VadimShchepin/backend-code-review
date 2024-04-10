<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    public function test_list(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertJson($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData); // Ensure decoded content is an array
        $this->assertArrayHasKey("messages", $responseData);
    }
    public function test_list_by_status_sent(): void
    {
        $client = static::createClient();

        $status = "SENT";
        $client->request('GET', "/messages?status={$status}");
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey("messages", $responseData);

        foreach ($responseData['messages'] as $message) {
            $this->assertEquals($status, $message['status']);
        }
    }
    function test_that_it_sends_a_message(): void
    {

        $client = static::createClient();

        //send message as JSON
        $data = ['text' => 'Test Post Message '];
        $content = json_encode($data);

        // Check if json_encode returned false and fail the test if it did
        if (!$content) {
            $this->fail('Failed to encode data to JSON.');
        }

        $client->request(
            method: 'POST',
            uri:'/messages/send',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $content
        );


        $this->assertResponseIsSuccessful();

        // This is using https://packagist.org/packages/zenstruck/messenger-test
        // package should be updated "Not supporting DelayStamp is deprecated"
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}