<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\ProgramPersistenceException;
use App\Models\Program;
use App\DTOs\Domain\Filters\ProgramFilter;
use App\Models\ProgramItem;
use App\DTOs\Domain\Filters\ProgramItemFilter;
use App\Repositories\Interfaces\IProgramRepository;

/**
 * Manages the Program and ProgramItem tables. A Program is a shopping cart tied to either
 * an anonymous browser session (via SessionKey) or an authenticated user. ProgramItems are
 * the individual session selections within a program, each with a quantity and optional
 * donation. Once checkout completes, the program is marked as checked out and an Order is created.
 */
class ProgramRepository extends BaseRepository implements IProgramRepository
{
    /**
     * Retrieves programs matching the given filter criteria (by ID, session key, user, or
     * checkout status). Returns newest programs first.
     *
     * @return Program[] Empty array if no programs match.
     */
    public function findPrograms(ProgramFilter $filter): array
    {
        $conditions = [];
        $params = [];

        if ($filter->programId !== null) {
            $conditions[] = 'p.ProgramId = :programId';
            $params['programId'] = $filter->programId;
        }

        if ($filter->sessionKey !== null) {
            $conditions[] = 'p.SessionKey = :sessionKey';
            $params['sessionKey'] = $filter->sessionKey;
        }

        if ($filter->userAccountId !== null) {
            $conditions[] = 'p.UserAccountId = :userAccountId';
            $params['userAccountId'] = $filter->userAccountId;
        }

        if ($filter->isCheckedOut !== null) {
            $conditions[] = 'p.IsCheckedOut = :isCheckedOut';
            $params['isCheckedOut'] = $filter->isCheckedOut ? 1 : 0;
        }

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $sql = "SELECT * FROM Program p {$whereClause} ORDER BY p.CreatedAtUtc DESC";

        return $this->fetchAll($sql, $params, fn(array $row) => Program::fromRow($row));
    }

    /**
     * Retrieves program items (cart line items) matching the given filter criteria.
     *
     * @return ProgramItem[] Ordered by ProgramItemId ascending. Empty array if no matches.
     */
    public function findProgramItems(ProgramItemFilter $filter): array
    {
        $conditions = [];
        $params = [];

        if ($filter->programId !== null) {
            $conditions[] = 'pi.ProgramId = :programId';
            $params['programId'] = $filter->programId;
        }

        if ($filter->programItemId !== null) {
            $conditions[] = 'pi.ProgramItemId = :programItemId';
            $params['programItemId'] = $filter->programItemId;
        }

        if ($filter->eventSessionId !== null) {
            $conditions[] = 'pi.EventSessionId = :eventSessionId';
            $params['eventSessionId'] = $filter->eventSessionId;
        }

        if ($filter->priceTierId !== null) {
            $conditions[] = 'pi.PriceTierId = :priceTierId';
            $params['priceTierId'] = $filter->priceTierId;
        }

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $sql = "SELECT * FROM ProgramItem pi {$whereClause} ORDER BY pi.ProgramItemId ASC";

        return $this->fetchAll($sql, $params, fn(array $row) => ProgramItem::fromRow($row));
    }

    /**
     * Creates a new empty program (cart) and immediately fetches it back to return
     * a fully populated model including server-generated defaults (e.g. CreatedAtUtc).
     *
     * @param string $sessionKey Browser session identifier for anonymous users.
     * @param int|null $userAccountId Null for guest users, set for authenticated users.
     * @throws ProgramPersistenceException If the inserted row cannot be read back.
     */
    public function createProgram(string $sessionKey, ?int $userAccountId): Program
    {
        $programId = $this->executeInsert(
            'INSERT INTO Program (SessionKey, UserAccountId, IsCheckedOut)
            VALUES (:sessionKey, :userAccountId, 0)',
            ['sessionKey' => $sessionKey, 'userAccountId' => $userAccountId],
        );

        $programs = $this->findPrograms(new ProgramFilter(programId: $programId));

        if ($programs === []) {
            throw new ProgramPersistenceException("Failed to retrieve program after creation (ID: {$programId})");
        }

        return $programs[0];
    }

    /**
     * Adds an event session to the program cart and returns the newly created item
     * with server-generated fields populated.
     *
     * @throws ProgramPersistenceException If the inserted row cannot be read back.
     */
    public function addItem(int $programId, int $eventSessionId, int $quantity, int $priceTierId, float $donationAmount): ProgramItem
    {
        $itemId = $this->executeInsert(
            'INSERT INTO ProgramItem (ProgramId, EventSessionId, Quantity, PriceTierId, DonationAmount)
            VALUES (:programId, :eventSessionId, :quantity, :priceTierId, :donationAmount)',
            [
                'programId' => $programId,
                'eventSessionId' => $eventSessionId,
                'quantity' => $quantity,
                'priceTierId' => $priceTierId,
                'donationAmount' => $donationAmount,
            ],
        );

        $items = $this->findProgramItems(new ProgramItemFilter(programItemId: $itemId));

        if ($items === []) {
            throw new ProgramPersistenceException("Failed to retrieve program item after creation (ID: {$itemId})");
        }

        return $items[0];
    }

    /**
     * Adds a pass to the program cart and returns the newly created item
     * with server-generated fields populated.
     *
     * @throws ProgramPersistenceException If the inserted row cannot be read back.
     */
    public function addPassItem(int $programId, int $passTypeId, ?string $passValidDate, int $quantity, float $donationAmount): ProgramItem
    {
        $itemId = $this->executeInsert(
            'INSERT INTO ProgramItem (ProgramId, PassTypeId, PassValidDate, Quantity, DonationAmount)
            VALUES (:programId, :passTypeId, :passValidDate, :quantity, :donationAmount)',
            [
                'programId' => $programId,
                'passTypeId' => $passTypeId,
                'passValidDate' => $passValidDate,
                'quantity' => $quantity,
                'donationAmount' => $donationAmount,
            ],
        );

        $items = $this->findProgramItems(new ProgramItemFilter(programItemId: $itemId));

        if ($items === []) {
            throw new ProgramPersistenceException("Failed to retrieve program item after creation (ID: {$itemId})");
        }

        return $items[0];
    }

    /**
     * Updates the ticket quantity for a cart item (e.g. user changes "2 tickets" to "3").
     */
    public function updateItemQuantity(int $programItemId, int $quantity): void
    {
        $this->execute(
            'UPDATE ProgramItem SET Quantity = :quantity WHERE ProgramItemId = :programItemId',
            ['quantity' => $quantity, 'programItemId' => $programItemId],
        );
    }

    /**
     * Updates the voluntary donation amount for a pay-what-you-like cart item.
     */
    public function updateItemDonation(int $programItemId, float $donationAmount): void
    {
        $this->execute(
            'UPDATE ProgramItem SET DonationAmount = :donationAmount WHERE ProgramItemId = :programItemId',
            ['donationAmount' => $donationAmount, 'programItemId' => $programItemId],
        );
    }

    /**
     * Removes a single item from the cart.
     */
    public function removeItem(int $programItemId): void
    {
        $this->execute(
            'DELETE FROM ProgramItem WHERE ProgramItemId = :programItemId',
            ['programItemId' => $programItemId],
        );
    }

    /**
     * Removes all items from a program, effectively emptying the cart.
     */
    public function clearProgram(int $programId): void
    {
        $this->execute(
            'DELETE FROM ProgramItem WHERE ProgramId = :programId',
            ['programId' => $programId],
        );
    }

    /**
     * Flags the program as checked out, preventing further modifications.
     * Called after an Order has been successfully created from this program.
     */
    public function markCheckedOut(int $programId): void
    {
        $this->execute(
            'UPDATE Program SET IsCheckedOut = 1 WHERE ProgramId = :programId',
            ['programId' => $programId],
        );
    }

    /**
     * Adds a reservation to the program cart and returns the newly created item
     * with server-generated fields populated.
     *
     * @throws ProgramPersistenceException If the inserted row cannot be read back.
     */
    public function addReservationItem(int $programId, int $reservationId, int $quantity): ProgramItem
    {
        $itemId = $this->executeInsert(
            'INSERT INTO ProgramItem (ProgramId, ReservationId, Quantity)
            VALUES (:programId, :reservationId, :quantity)',
            [
                'programId' => $programId,
                'reservationId' => $reservationId,
                'quantity' => $quantity,
            ],
        );

        $items = $this->findProgramItems(new ProgramItemFilter(programItemId: $itemId));

        if ($items === []) {
            throw new ProgramPersistenceException("Failed to retrieve program item after creation (ID: {$itemId})");
        }

        return $items[0];
    }
}
