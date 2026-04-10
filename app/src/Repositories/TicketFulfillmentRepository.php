<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Domain\Filters\EventSessionFilter;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\DTOs\Domain\Tickets\TicketRecipient;
use App\Exceptions\RepositoryException;
use App\Exceptions\TicketDeliveryException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\ITicketFulfillmentRepository;
use App\Repositories\Interfaces\ITicketRepository;
use App\Repositories\Interfaces\IUserAccountRepository;
use App\Utils\TicketCodeGenerator;

class TicketFulfillmentRepository implements ITicketFulfillmentRepository
{
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly ITicketRepository $ticketRepository,
        private readonly IUserAccountRepository $userAccountRepository,
        private readonly TicketCodeGenerator $ticketCodeGenerator,
    ) {}

    public function findOrder(int $orderId): ?Order
    {
        return $this->orderRepository->findById($orderId);
    }

    public function updateTicketRecipient(int $orderId, string $firstName, string $lastName, string $email): void
    {
        $this->orderRepository->updateTicketRecipient($orderId, $firstName, $lastName, $email);
    }

    public function markTicketEmailSent(int $orderId, \DateTimeImmutable $sentAtUtc): void
    {
        $this->orderRepository->markTicketEmailSent($orderId, $sentAtUtc);
    }

    public function markTicketEmailFailed(int $orderId, string $errorMessage): void
    {
        $this->orderRepository->markTicketEmailFailed($orderId, $errorMessage);
    }

    public function resetTicketEmailState(int $orderId): void
    {
        $this->orderRepository->resetTicketEmailState($orderId);
    }

    public function findTicketableOrderItems(int $orderId): array
    {
        return array_values(array_filter(
            $this->orderItemRepository->findByOrderId($orderId),
            static fn(OrderItem $item) => $item->eventSessionId !== null && $item->eventSessionId > 0 && $item->quantity > 0,
        ));
    }

    public function findOrderItemById(int $orderItemId): ?OrderItem
    {
        return $this->orderItemRepository->findById($orderItemId);
    }

    public function findSessionsByIds(array $sessionIds): array
    {
        if ($sessionIds === []) {
            return [];
        }

        $result = $this->eventSessionRepository->findSessions(new EventSessionFilter(
            sessionIds: $sessionIds,
            includeCancelled: true,
            limit: count($sessionIds),
        ));

        $sessionsById = [];
        foreach ($result->sessions as $session) {
            $sessionsById[$session->eventSessionId] = $session;
        }

        return $sessionsById;
    }

    public function resolveRecipient(Order $order, ?string $fallbackEmail, ?string $fallbackFirstName, ?string $fallbackLastName): TicketRecipient
    {
        $user = $this->userAccountRepository->findById($order->userAccountId);
        $email = $this->firstNonEmpty($order->ticketRecipientEmail, $fallbackEmail, $user?->email);

        if ($email === null) {
            throw new TicketDeliveryException('Paid order is missing a ticket recipient email address.');
        }

        $firstName = $this->firstNonEmpty($order->ticketRecipientFirstName, $fallbackFirstName, $user?->firstName) ?? '';
        $lastName = $this->firstNonEmpty($order->ticketRecipientLastName, $fallbackLastName, $user?->lastName) ?? '';
        $displayName = trim($firstName . ' ' . $lastName);

        return new TicketRecipient(
            email: $email,
            firstName: $firstName,
            lastName: $lastName,
            displayName: $displayName !== '' ? $displayName : $email,
        );
    }

    public function findTicketsByOrderItemIds(array $orderItemIds): array
    {
        $tickets = $this->ticketRepository->findByOrderItemIds($orderItemIds);

        $grouped = [];
        foreach ($tickets as $ticket) {
            $grouped[$ticket->orderItemId][] = $ticket;
        }

        return $grouped;
    }

    public function findTicketByCode(string $ticketCode): ?Ticket
    {
        return $this->ticketRepository->findByCode($ticketCode);
    }

    public function createTicket(int $orderItemId, int $maxAttempts): Ticket
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $ticketCode = $this->ticketCodeGenerator->generate();

            try {
                $ticketId = $this->ticketRepository->create($orderItemId, $ticketCode);

                return new Ticket(
                    ticketId: $ticketId,
                    orderItemId: $orderItemId,
                    ticketCode: $ticketCode,
                    isScanned: false,
                    scannedAtUtc: null,
                    scannedByUserId: null,
                    pdfAssetId: null,
                );
            } catch (RepositoryException $error) {
                if (!str_contains(strtolower($error->getMessage()), 'duplicate')) {
                    throw $error;
                }
            }
        }

        throw new TicketDeliveryException('A unique ticket code could not be generated.');
    }

    public function updateTicketPdfAssetId(int $ticketId, int $assetId): void
    {
        $this->ticketRepository->updatePdfAssetId($ticketId, $assetId);
    }

    private function firstNonEmpty(?string ...$values): ?string
    {
        foreach ($values as $value) {
            if ($value !== null && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }
}
