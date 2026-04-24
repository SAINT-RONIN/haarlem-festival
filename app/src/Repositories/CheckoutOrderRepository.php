<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Repositories\Interfaces\ICheckoutOrderRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\IPassPurchaseRepository;
use App\Repositories\Interfaces\IPaymentRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Services\Interfaces\IOrderCapacityRestorer;
use PDO;

/**
 * Transactional persistence for the checkout flow.
 *
 * Encapsulates the multi-table mutations that checkout triggers (Order + OrderItem +
 * PassPurchase + Payment + capacity + program) behind single methods.
 */
class CheckoutOrderRepository implements ICheckoutOrderRepository
{
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IPaymentRepository $paymentRepository,
        private readonly IPassPurchaseRepository $passPurchaseRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly IProgramRepository $programRepository,
        private readonly IOrderCapacityRestorer $orderCapacityRestorer,
        private readonly PDO $pdo,
    ) {}

    public function createOrder(
        int $userId,
        int $programId,
        string $orderNumber,
        string $subtotal,
        string $vatTotal,
        string $totalAmount,
        string $firstName,
        string $lastName,
        string $email,
        \DateTimeImmutable $payBeforeUtc,
        PaymentMethod $paymentMethod,
        string $vatRate,
        array $items,
    ): array {
        $this->pdo->beginTransaction();

        try {
            $orderId = $this->orderRepository->create(
                userAccountId: $userId,
                programId: $programId,
                orderNumber: $orderNumber,
                subtotal: $subtotal,
                vatTotal: $vatTotal,
                totalAmount: $totalAmount,
                ticketRecipientFirstName: $firstName,
                ticketRecipientLastName: $lastName,
                ticketRecipientEmail: $email,
                payBeforeUtc: $payBeforeUtc,
            );

            foreach ($items as $item) {
                $this->persistItem($orderId, $userId, $vatRate, $item);
            }

            foreach ($items as $item) {
                $this->reserveSeats($item);
            }

            $paymentId = $this->paymentRepository->create($orderId, $paymentMethod, PaymentStatus::Pending);

            $this->pdo->commit();

            return ['orderId' => $orderId, 'paymentId' => $paymentId];
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }

    public function linkStripeIds(int $paymentId, string $stripeSessionId, string $paymentIntentId): void
    {
        $this->paymentRepository->updateStripeSessionId($paymentId, $stripeSessionId);
        $this->paymentRepository->updateProviderRef($paymentId, $stripeSessionId);

        if ($paymentIntentId !== '') {
            $this->paymentRepository->updateStripePaymentIntentId($paymentId, $paymentIntentId);
        }
    }

    public function createRetryPayment(int $orderId, PaymentMethod $method): int
    {
        return $this->paymentRepository->create($orderId, $method, PaymentStatus::Pending);
    }

    public function cancelOrder(int $orderId, int $paymentId): void
    {
        $this->orderCapacityRestorer->restore($orderId);
        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Cancelled, [OrderStatus::Pending]);
        $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Cancelled, [PaymentStatus::Pending]);
    }

    public function markOrderPaid(int $orderId, int $paymentId, int $programId, \DateTimeImmutable $paidAtUtc): void
    {
        $this->orderRepository->updateStatusIfCurrentIn($orderId, OrderStatus::Paid, [OrderStatus::Pending]);
        $this->paymentRepository->updateStatusIfCurrentIn($paymentId, PaymentStatus::Paid, [PaymentStatus::Pending], $paidAtUtc);

        if ($programId > 0) {
            $this->programRepository->markCheckedOut($programId);
        }
    }

    public function findOrderByIdAndUserId(int $orderId, int $userId): ?Order
    {
        return $this->orderRepository->findByIdAndUserId($orderId, $userId);
    }

    /**
     * @param array{eventSessionId: ?int, passTypeId: ?int, quantity: int, seatCount: int, basePrice: float, donationAmount: float, passValidDate: ?string} $item
     */
    private function persistItem(int $orderId, int $userId, string $vatRate, array $item): void
    {
        $unitPrice = number_format($item['basePrice'], 2, '.', '');
        $donationAmount = number_format($item['donationAmount'], 2, '.', '');

        if ($item['passTypeId'] !== null) {
            $passPurchaseId = $this->passPurchaseRepository->create(
                passTypeId: $item['passTypeId'],
                userAccountId: $userId,
                validDate: $item['passValidDate'],
                validFromDate: null,
                validToDate: null,
            );

            $this->orderItemRepository->create(
                orderId: $orderId,
                eventSessionId: null,
                historyTourId: null,
                passPurchaseId: $passPurchaseId,
                quantity: $item['quantity'],
                unitPrice: $unitPrice,
                vatRate: $vatRate,
                donationAmount: $donationAmount,
            );

            return;
        }

        $this->orderItemRepository->create(
            orderId: $orderId,
            eventSessionId: $item['eventSessionId'],
            historyTourId: null,
            passPurchaseId: null,
            quantity: $item['quantity'],
            unitPrice: $unitPrice,
            vatRate: $vatRate,
            donationAmount: $donationAmount,
        );
    }

    /**
     * @param array{eventSessionId: ?int, seatCount: int} $item
     */
    private function reserveSeats(array $item): void
    {
        if ($item['eventSessionId'] === null || $item['eventSessionId'] <= 0) {
            return;
        }

        $reserved = $this->eventSessionRepository->decrementCapacity($item['eventSessionId'], $item['seatCount']);

        if (!$reserved) {
            throw new \RuntimeException('Seats no longer available. Please update your program.');
        }
    }
}
