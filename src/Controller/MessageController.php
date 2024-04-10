<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class MessageController extends AbstractController
{
    //It's better always to include rote methods should be used
    //It's possible to make route like /messages/criteria/{$criteria} for filtering
    #[Route('/messages', methods: ['GET'])]
    public function listMessages(Request $request, MessageRepository $messages): JsonResponse
    {
        $status = (string)$request->query->get('status');
        //if purpose was just to use findBy instead of by, the standard method can be used
        $messageEntities = $messages->findByStatus($status);
        if (!$messageEntities) {
            return new JsonResponse(['messages' => []], 200);
        }

        $messageData = $this->formatMessageData($messageEntities);
//Json Response sets headers automatically as needed, is more readable and type safe
        return new JsonResponse(['messages' => $messageData]);

    }

    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        if ($request->headers->get('Content-Type') !== 'application/json') {
            return new Response('Unsupported Media Type', 415);
        }
        //text from query is not type safe, must be encoded, escaped
        //more safe and common approach to retrieve the text from post request body
        $content = json_decode($request->getContent(),true);

        if (!is_array($content)) {
            // Handle the error appropriately, perhaps by returning a 400 Bad Request response
            return new Response('Invalid JSON data', 400);
        }
        $messageText = $this->checkAndRetrieveData($content);

        if ($messageText === null) {
            return new Response('Text is required and must be of type string', 422);
        }

        $bus->dispatch(new SendMessage($messageText));
        // Status 204 "No Content" is not the best choice
        // 201 would make sense without pubsub between req and creation
        return new Response('Successfully sent', 200);
    }
    //extract some logic to make controllers more readable and have abstraction level balanced
    //The logic is not big enough to create a service
    /**
     * @param array{text?: string} $data Array . 'text' key expected to be a string.
     */
    private function checkAndRetrieveData(array $data): ?string
    {
        /**
         * @phpstan-ignore-next-line
         * Negated boolean expression is always false. can't understand why, but this validation is important
         */
        if (empty($data['text']) || !is_string($data['text'])) {
            return null;
        }
        return $data['text'];
    }
    /**
     * Formats message entities into an array.
     *
     * @param Message[] $messageEntities Array of Message entities.
     * @return array<array{uuid: string|null, text: string|null, status: 'failed'|'read'|'sent'}>
     */
    private function formatMessageData(array $messageEntities): array
    {
        $messageData = [];
        foreach ($messageEntities as $message) {
            $messageData[] = [
                'uuid' => $message->getUuid(), // Assuming getUuid() can return null
                'text' => $message->getText(), // Assuming getText() can return null
                'status' => $message->getStatus()->value, // This is fine as is
            ];
        }
        return $messageData;
    }
}