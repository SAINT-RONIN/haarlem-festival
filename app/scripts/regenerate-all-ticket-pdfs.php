<?php

declare(strict_types=1);

use App\Infrastructure\Database;
use App\Infrastructure\EmailService;
use App\Infrastructure\PdfAssetStorage;
use App\Repositories\EventSessionRepository;
use App\Repositories\MediaAssetRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserAccountRepository;
use App\Services\TicketFulfillmentService;
use App\Tickets\PdfTicketGenerator;
use App\Tickets\QrCodeGenerator;
use App\Tickets\TicketCodeGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$envPath = dirname(__DIR__, 2) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }

        putenv(trim($line));
    }
}

try {
    $pdo = Database::getConnection();
    $service = new TicketFulfillmentService(
        new OrderRepository($pdo),
        new OrderItemRepository($pdo),
        new EventSessionRepository($pdo),
        new TicketRepository($pdo),
        new MediaAssetRepository($pdo),
        new UserAccountRepository($pdo),
        new EmailService(),
        new PdfAssetStorage(new MediaAssetRepository($pdo)),
        new QrCodeGenerator(),
        new PdfTicketGenerator(),
        new TicketCodeGenerator(),
    );

    $statement = $pdo->query('SELECT TicketCode FROM Ticket ORDER BY TicketId ASC');
    $ticketCodes = $statement !== false ? $statement->fetchAll(PDO::FETCH_COLUMN) : [];
    $successCount = 0;
    $errorCount = 0;

    foreach ($ticketCodes as $ticketCode) {
        try {
            $service->regenerateTicketDocumentsByTicketCode((string)$ticketCode);
            fwrite(STDOUT, "Regenerated {$ticketCode}\n");
            $successCount++;
        } catch (Throwable $error) {
            fwrite(STDERR, "Failed {$ticketCode}: {$error->getMessage()}\n");
            $errorCount++;
        }
    }

    fwrite(STDOUT, "Completed. Regenerated {$successCount} ticket PDF set(s)");
    if ($errorCount > 0) {
        fwrite(STDOUT, ", {$errorCount} failed");
    }
    fwrite(STDOUT, ".\n");
    exit($errorCount > 0 ? 1 : 0);
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
