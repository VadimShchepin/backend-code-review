<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Controller\MessageControllerTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */
    //It's better always to include methods which should be used
    #[Route('/messages',methods: ['GET'])]
    public function list(Request $request, MessageRepository $messages): JsonResponse
    {
        $status = (string)$request->query->get('status');

        $messageEntities = $messages->findByStatus($status);

        $messageData = [];
        foreach ($messageEntities as $message) {
            $messageData[] = [
                'uuid' => $message->getUuid(),
                'text' => $message->getText(),
                'status' => $message->getStatus()->value,
            ];
        }
//Json Response sets headers automatically as needed, is more readable and type safe
        return new JsonResponse(['messages' => $messageData]);

    }

    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $text = (string)$request->query->get('text');
        
        if (!$text) {
            return new Response('Text is required', 400);
        }

        $bus->dispatch(new SendMessage($text));
        
        return new Response('Successfully sent', 204);
    }
}