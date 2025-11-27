<?php

namespace App\Controller;

use App\Entity\User;
use App\Messenger\Message\UserMessage;
use App\Service\Validation\User\UserValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface    $messageBus,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserValidatorInterface $validator
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws \JsonException
     */
    #[Route('/create', name: 'api_user_create', methods: ['POST'])]
    #[OA\Post(
        summary: "Create new user asynchronously",
        requestBody: new OA\RequestBody(
            description: "User registration payload",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", example: "ivan@example.com"),
                    new OA\Property(property: "password", type: "string", example: "test123"),
                    new OA\Property(property: "firstName", type: "string", example: "Ivan"),
                    new OA\Property(property: "lastName", type: "string", example: "Ivanov"),
                    new OA\Property(property: "phoneNumbers", type: "array", items: new OA\Items(type: "string"), example: ["+380671234567"]),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(response: 202, description: "Request accepted and queued for background processing."),
            new OA\Response(response: 400, description: "Validation error or missing fields.")
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($data);

        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $ip = $request->getClientIp() ?? '127.0.0.1';
            $message = new UserMessage($data, $ip);

            $this->messageBus->dispatch($message);

            return new JsonResponse(['status' => 'success'], Response::HTTP_ACCEPTED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/list', name: 'api_user_list', methods: ['GET'])]
    #[OA\Get(
        summary: "List users with sorting",
        parameters: [
            new OA\Parameter(
                name: "sort",
                description: "Field to sort by (id, firstName, lastName, email)",
                in: "query",
                schema: new OA\Schema(type: "string", enum: ["id", "firstName", "lastName", "email"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Returns a list of users.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: new Model(type: User::class))
                )
            )
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        $sort = $request->query->get('sort');

        $users = $this->entityManager->getRepository(User::class)->findAllSorted($sort);

        return $this->json($users);
    }
}