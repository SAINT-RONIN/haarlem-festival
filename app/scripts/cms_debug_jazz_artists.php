<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Repositories\CmsRepository;

$repo = new CmsRepository();
$page = $repo->getPageBySlug('jazz');
if ($page === null) {
    fwrite(STDERR, "No CmsPage with slug 'jazz' found\n");
    exit(1);
}

echo "Jazz page: CmsPageId=" . $page['CmsPageId'] . " Title=" . ($page['Title'] ?? '') . PHP_EOL;

$sections = $repo->getSectionsByPageId((int) $page['CmsPageId']);

echo "Sections for jazz:" . PHP_EOL;
foreach ($sections as $section) {
    echo "- CmsSectionId=" . $section['CmsSectionId'] . " Key=" . $section['SectionKey'] . PHP_EOL;
}

echo PHP_EOL;

foreach ($sections as $section) {
    if (stripos((string) $section['SectionKey'], 'artist') === false) {
        continue;
    }

    echo "Section: CmsSectionId=" . $section['CmsSectionId'] . " Key=" . $section['SectionKey'] . PHP_EOL;
    $items = $repo->getItemsBySectionId((int) $section['CmsSectionId']);
    if (count($items) === 0) {
        echo "  (no items)" . PHP_EOL;
        continue;
    }

    foreach ($items as $item) {
        echo "  - ItemId=" . $item['CmsItemId'] . " Key=" . $item['ItemKey'] . " Type=" . $item['ItemType'] . " MediaAssetId=" . ($item['MediaAssetId'] ?? 'NULL') . PHP_EOL;
    }
    echo PHP_EOL;
}
