<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database;

$db = Database::getInstance();

$stmt = $db->prepare(
    "SELECT CmsItemId, TextValue \
     FROM CmsItem \
     WHERE ItemType = 'TEXT' \
       AND TextValue IS NOT NULL \
       AND TextValue LIKE '%<%>%'"
);
$stmt->execute();
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

$updated = 0;
foreach ($rows as $row) {
    $id = (int)$row['CmsItemId'];
    $current = (string)$row['TextValue'];

    $decoded = html_entity_decode($current, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $plain = trim(strip_tags($decoded));

    if ($plain === $current) {
        continue;
    }

    $u = $db->prepare(
        "UPDATE CmsItem \
         SET TextValue = ?, HtmlValue = NULL, UpdatedAtUtc = UTC_TIMESTAMP() \
         WHERE CmsItemId = ?"
    );
    $u->execute([$plain, $id]);
    $updated++;
}

echo "Found candidates: " . count($rows) . PHP_EOL;
echo "Updated TEXT items: {$updated}" . PHP_EOL;
