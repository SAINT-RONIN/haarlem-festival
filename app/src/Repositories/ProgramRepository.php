<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Program;
use App\Models\ProgramFilter;
use App\Models\ProgramItem;
use App\Models\ProgramItemFilter;
use App\Repositories\Interfaces\IProgramRepository;
use PDO;

class ProgramRepository implements IProgramRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * @return Program[]
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Program::class, 'fromRow'], $rows);
    }

    /**
     * @return ProgramItem[]
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

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $sql = "SELECT * FROM ProgramItem pi {$whereClause} ORDER BY pi.ProgramItemId ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([ProgramItem::class, 'fromRow'], $rows);
    }

    public function createProgram(string $sessionKey, ?int $userAccountId): Program
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO Program (SessionKey, UserAccountId, IsCheckedOut)
            VALUES (:sessionKey, :userAccountId, 0)
        ');

        $stmt->execute([
            'sessionKey' => $sessionKey,
            'userAccountId' => $userAccountId,
        ]);

        $programId = (int)$this->pdo->lastInsertId();

        $programs = $this->findPrograms(new ProgramFilter(programId: $programId));

        return $programs[0];
    }

    public function addItem(int $programId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO ProgramItem (ProgramId, EventSessionId, Quantity, DonationAmount)
            VALUES (:programId, :eventSessionId, :quantity, :donationAmount)
        ');

        $stmt->execute([
            'programId' => $programId,
            'eventSessionId' => $eventSessionId,
            'quantity' => $quantity,
            'donationAmount' => $donationAmount,
        ]);

        $itemId = (int)$this->pdo->lastInsertId();

        $items = $this->findProgramItems(new ProgramItemFilter(programItemId: $itemId));

        return $items[0];
    }

    public function updateItemQuantity(int $programItemId, int $quantity): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE ProgramItem SET Quantity = :quantity
            WHERE ProgramItemId = :programItemId
        ');

        $stmt->execute([
            'quantity' => $quantity,
            'programItemId' => $programItemId,
        ]);
    }

    public function updateItemDonation(int $programItemId, float $donationAmount): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE ProgramItem SET DonationAmount = :donationAmount
            WHERE ProgramItemId = :programItemId
        ');

        $stmt->execute([
            'donationAmount' => $donationAmount,
            'programItemId' => $programItemId,
        ]);
    }

    public function removeItem(int $programItemId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM ProgramItem WHERE ProgramItemId = :programItemId');
        $stmt->execute(['programItemId' => $programItemId]);
    }

    public function clearProgram(int $programId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM ProgramItem WHERE ProgramId = :programId');
        $stmt->execute(['programId' => $programId]);
    }

    public function markCheckedOut(int $programId): void
    {
        $stmt = $this->pdo->prepare('UPDATE Program SET IsCheckedOut = 1 WHERE ProgramId = :programId');
        $stmt->execute(['programId' => $programId]);
    }
}
