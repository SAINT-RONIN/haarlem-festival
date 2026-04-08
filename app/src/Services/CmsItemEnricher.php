<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CmsItemType;
use App\Models\CmsItem;
use App\DTOs\Cms\CmsItemEditData;
use App\DTOs\Cms\CmsMediaAssetData;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\ICmsItemEnricher;
use App\Utils\CmsContentLimits;

/**
 * Enriches raw CmsItem records with media-asset metadata and editor-input type info
 * so the CMS editor UI has everything it needs to render each field correctly.
 */
final class CmsItemEnricher implements ICmsItemEnricher
{
    public function __construct(
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {}

    /**
     * @param CmsItem[] $items
     * @return CmsItemEditData[]
     */
    public function enrichItems(array $items): array
    {
        $mediaAssets = $this->loadMediaAssetsForItems($items);
        $enriched = [];

        foreach ($items as $item) {
            $enriched[] = $this->enrichSingleItem($item, $mediaAssets);
        }

        return $enriched;
    }

    /**
     * @param array<int, MediaAsset> $mediaAssets
     */
    private function enrichSingleItem(CmsItem $item, array $mediaAssets): CmsItemEditData
    {
        $mediaAsset = $this->resolveMediaAsset($item, $mediaAssets);
        $resolvedFilePath = $this->resolveFilePath($item, $mediaAsset);
        $inputType = $this->resolveInputType($item);

        return $this->buildEnrichedItem($item, $mediaAsset, $resolvedFilePath, $inputType);
    }

    /**
     * Batch-loads media assets for all items that reference one.
     *
     * @param CmsItem[] $items
     * @return array<int, MediaAsset>
     */
    private function loadMediaAssetsForItems(array $items): array
    {
        $mediaAssetIds = array_values(array_filter(
            array_map(fn(CmsItem $item) => $item->mediaAssetId, $items)
        ));

        return $mediaAssetIds !== [] ? $this->mediaAssetRepository->findByIds($mediaAssetIds) : [];
    }

    private function resolveMediaAsset(CmsItem $item, array $mediaAssets): ?MediaAsset
    {
        return $item->mediaAssetId !== null ? ($mediaAssets[$item->mediaAssetId] ?? null) : null;
    }

    private function resolveFilePath(CmsItem $item, ?MediaAsset $mediaAsset): ?string
    {
        if ($mediaAsset !== null && $mediaAsset->filePath !== '') {
            return $mediaAsset->filePath;
        }

        if (!empty($item->textValue)) {
            return (string) $item->textValue;
        }

        return null;
    }

    private function resolveInputType(CmsItem $item): string
    {
        $type = $item->itemType;
        $inputType = CmsContentLimits::getInputType($type->value);

        // Certain TEXT keys (e.g. long descriptions) are edited via TinyMCE instead of a plain input
        if ($type === CmsItemType::Text && CmsContentLimits::textKeyUsesTinyMce($item->itemKey)) {
            return 'tinymce';
        }

        return $inputType;
    }

    private function buildEnrichedItem(CmsItem $item, ?MediaAsset $mediaAsset, ?string $resolvedFilePath, string $inputType): CmsItemEditData
    {
        $type = $item->itemType;

        return new CmsItemEditData(
            itemId: $item->cmsItemId,
            itemKey: $item->itemKey,
            displayName: $item->itemKey,
            type: $type->value,
            typeLabel: CmsContentLimits::getLabelForType($type->value),
            inputType: $inputType,
            maxChars: CmsContentLimits::getCharLimitForType($type->value),
            value: $this->getItemValue($item),
            mediaAssetId: $item->mediaAssetId,
            mediaAsset: $this->buildMediaAssetData($item, $mediaAsset, $resolvedFilePath, $type),
        );
    }

    private function buildMediaAssetData(CmsItem $item, ?MediaAsset $mediaAsset, ?string $resolvedFilePath, CmsItemType $type): ?CmsMediaAssetData
    {
        if ($mediaAsset !== null) {
            return $this->mediaAssetDataFromAsset($mediaAsset);
        }
        if ($resolvedFilePath !== null && $type === CmsItemType::ImagePath) {
            return $this->mediaAssetDataFromFilePath($item->itemKey, $resolvedFilePath);
        }
        return null;
    }

    private function mediaAssetDataFromAsset(MediaAsset $mediaAsset): CmsMediaAssetData
    {
        return new CmsMediaAssetData(
            filePath: $mediaAsset->filePath,
            originalFileName: $mediaAsset->originalFileName,
            altText: $mediaAsset->altText,
        );
    }

    private function mediaAssetDataFromFilePath(string $itemKey, string $filePath): CmsMediaAssetData
    {
        return new CmsMediaAssetData(
            filePath: $filePath,
            originalFileName: basename($filePath),
            altText: $itemKey,
        );
    }

    /**
     * Extracts the display value for the CMS editor. HTML items use htmlValue directly;
     * TEXT items that were accidentally stored with HTML tags get stripped back to plain text.
     */
    private function getItemValue(CmsItem $item): string
    {
        $type = $item->itemType;

        if ($type === CmsItemType::Html) {
            return (string) ($item->htmlValue ?? '');
        }

        $value = (string) ($item->textValue ?? '');
        if ($type === CmsItemType::Text && $value !== '' && preg_match('/<[^>]+>/', $value) === 1) {
            return trim(strip_tags(html_entity_decode($value)));
        }

        return $value;
    }
}
