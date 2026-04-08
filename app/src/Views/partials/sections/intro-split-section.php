<?php
/**
 * Global Intro Split section partial.
 * Text on one side, image on the other.
 *
 * Default layout: text LEFT, image RIGHT.
 * Set $introSplitReversed = true to flip: image LEFT, text RIGHT.
 *
 * Required variable:
 * @var \App\ViewModels\IntroSplitSectionData $introSplitSection
 *
 * Optional variables:
 * @var string|null $sectionId           HTML id for the section
 * @var string|null $introSplitImageClass CSS class for the image
 * @var bool        $introSplitReversed  Flip the layout (image left, text right)
 */

if (!isset($introSplitSection)) {
    return;
}

$sectionId ??= 'intro';
$introSplitImageClass ??= 'w-full h-auto rounded-2xl object-cover';
$introSplitReversed ??= false;
$flexDirection = $introSplitReversed ? 'lg:flex-row-reverse' : 'lg:flex-row';

// Allow basic formatting from CMS while stripping unsupported tags.
$introBodyHtml = trim(strip_tags((string) $introSplitSection->bodyText, '<p><br><strong><em><ul><ol><li>'));
if ($introBodyHtml === '') {
    $introBodyHtml = nl2br(htmlspecialchars($introSplitSection->bodyText));
}
?>

<section id="<?= htmlspecialchars($sectionId) ?>"
         class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 flex flex-col <?= $flexDirection ?> justify-center items-stretch gap-6 sm:gap-8 md:gap-10 lg:gap-12">
    <div class="flex-1 flex flex-col justify-center items-start gap-4 sm:gap-5 md:gap-6">
        <h2 class="text-gray-900 text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
            <?= htmlspecialchars($introSplitSection->headingText) ?>
        </h2>
        <div class="w-full text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed [&>p]:m-0 [&>p+p]:mt-4 [&>ul]:my-4 [&>ol]:my-4 [&_li]:ml-6 [&_li]:list-disc">
            <?= $introBodyHtml ?>
        </div>

        <?php if (!empty($introSplitSection->subsections)) : ?>
            <div class="w-full flex flex-col gap-4 mt-2">
                <?php foreach ($introSplitSection->subsections as $subsection) : ?>
                    <div class="flex flex-col gap-1">
                        <p class="text-gray-700 text-base sm:text-lg leading-relaxed font-bold">
                            <?= htmlspecialchars((string) ($subsection['heading'] ?? '')) ?>
                        </p>
                        <p class="text-gray-700 text-base sm:text-lg leading-relaxed">
                            <?= htmlspecialchars((string) ($subsection['text'] ?? '')) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($introSplitSection->closingLine)) : ?>
            <p class="text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed mt-2">
                <?= htmlspecialchars($introSplitSection->closingLine) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="flex-1 flex justify-center items-center">
        <img src="<?= htmlspecialchars($introSplitSection->imageUrl) ?>"
             alt="<?= htmlspecialchars($introSplitSection->imageAltText) ?>"
             class="<?= htmlspecialchars($introSplitImageClass) ?>">
    </div>
</section>
