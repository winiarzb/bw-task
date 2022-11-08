<?php

namespace App\Controller;

use App\Repository\MeetingRepository;
use App\Service\MeetingStatusResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController
{
    public function __construct(
        private MeetingRepository    $meetingRepository,
        private MeetingStatusResolver $meetingStatusResolver
    ) {}

    #[Route('/meetings/{id}', name: 'meeting')]
    public function meeting(string $meetingId): Response
    {
        $meeting = $this->meetingRepository->get($meetingId);
        $meeting->setStatus($this->meetingStatusResolver->getStatus($meeting));

        return new JsonResponse($meeting);
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return new Response('<h1>Hello</h1>');
    }
}
