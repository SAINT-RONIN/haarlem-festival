<?php

declare(strict_types=1);

use App\Infrastructure\Database;
use App\Infrastructure\EmailService;
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

$ticketCode = strtoupper(trim((string)($argv[1] ?? '')));
if ($ticketCode === '') {
    fwrite(STDERR, "Usage: php scripts/regenerate-ticket-pdfs.php HF-XXXXXXXXXXXX\n");
    exit(1);
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
        new QrCodeGenerator(),
        new PdfTicketGenerator(),
        new TicketCodeGenerator(),
    );

    $service->regenerateTicketDocumentsByTicketCode($ticketCode);

    fwrite(STDOUT, "Regenerated ticket PDF assets for {$ticketCode}\n");
    exit(0);
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
