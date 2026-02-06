<?php
/**
 * Event type card partial - Single event type showcase card.
 *
 * @var array $eventType Event type data with keys: slug, title, description, darkBg
 * @var bool $imageFirst Whether image appears before text (alternating layout)
 */

$darkBg = $eventType['darkBg'];

$bgClass = $darkBg ? 'bg-slate-800' : 'bg-stone-100';
$textClass = $darkBg ? 'text-stone-100' : 'text-slate-800';
$titleClass = $darkBg ? 'text-stone-100' : 'text-slate-800 underline';

$buttonBg = 'bg-stone-100';
$buttonTextClass = 'text-slate-800';

/**
 * Button border:
 * - default border: slate
 * - hover border: sand (using a hex color so it always works)
 */
$buttonBorder = 'border border-slate-800 hover:border-[#F5F1EB]';

$title = htmlspecialchars($eventType['title']);
$description = htmlspecialchars($eventType['description']);
$slug = htmlspecialchars($eventType['slug']);

// Generate unique ID for accessibility
$cardId = 'event-type-' . $slug;

// TODO: Image paths should be retrieved from database (e.g., EventType.ImagePath or MediaAsset)
$imageMap = [
        'jazz' => 'Image (Jazz).png',
        'dance' => 'Image (Dance).png',
        'history' => 'Image (History).png',
        'restaurant' => 'Image (Yummy).png',
        'storytelling' => 'Image (Story).png',
];
$imageSrc = '/assets/Image/' . $imageMap[$slug];

// Alt text map for meaningful image descriptions
$altTextMap = [
        'jazz' => 'Jazz musicians performing live at Haarlem Festival',
        'dance' => 'Dancers performing at Haarlem Festival dance event',
        'history' => 'Historic buildings and walking tour in Haarlem',
        'restaurant' => 'Delicious food served at Haarlem Festival restaurants',
        'storytelling' => 'Storytelling performance at Haarlem Festival',
];
$imageAlt = $altTextMap[$slug] ?? $title . ' event';

// Button text from CMS database (e.g., "Explore Jazz Events")
$buttonLabel = htmlspecialchars($eventType['button']);
?>

<article class="self-stretch p-2 sm:p-3 md:p-4 lg:p-6 xl:p-8 2xl:p-10 <?php echo $bgClass; ?> rounded-[12px] sm:rounded-[15px] md:rounded-[25px] lg:rounded-[40px] flex flex-col lg:flex-row justify-center items-center gap-2 sm:gap-3 lg:gap-4 xl:gap-8 2xl:gap-12" aria-labelledby="<?php echo $cardId; ?>-heading">
    <?php if ($imageFirst): ?>
        <figure class="w-full lg:flex-1">
            <img class="w-full h-[140px] sm:h-[180px] md:h-[240px] lg:h-[220px] xl:h-[300px] 2xl:h-[400px] object-cover rounded-[10px] sm:rounded-[15px] md:rounded-[25px] lg:rounded-[40px]" src="<?php echo $imageSrc; ?>" alt="<?php echo $imageAlt; ?>">
        </figure>

        <div class="w-full lg:flex-1 flex flex-col justify-center items-start gap-1 sm:gap-1.5 lg:gap-2 xl:gap-2.5">
            <h3 id="<?php echo $cardId; ?>-heading" class="<?php echo $titleClass; ?> text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl 2xl:text-5xl font-bold leading-tight"><?php echo $title; ?></h3>
            <p class="self-stretch <?php echo $textClass; ?> text-[11px] sm:text-xs md:text-sm lg:text-base xl:text-lg 2xl:text-xl font-normal leading-4 sm:leading-5 md:leading-6 lg:leading-7"><?php echo $description; ?></p>

            <a href="/<?php echo $slug; ?>"
               class="p-1 sm:p-1.5 md:p-2 lg:p-2.5 xl:p-3 2xl:p-3.5 <?php echo $buttonBg; ?> hover:bg-slate-800
                      rounded-md sm:rounded-lg md:rounded-xl lg:rounded-2xl
                      <?php echo $buttonBorder; ?>
                      inline-flex justify-center items-center transition-colors duration-200 group
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                <span class="text-center <?php echo $buttonTextClass; ?> group-hover:text-stone-100 text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-normal transition-colors duration-200">
                    <?php echo $buttonLabel; ?>
                </span>
            </a>
        </div>

    <?php else: ?>
        <div class="w-full lg:flex-1 flex flex-col justify-center items-start gap-1 sm:gap-1.5 lg:gap-2 xl:gap-2.5 order-2 lg:order-1">
            <h3 id="<?php echo $cardId; ?>-heading" class="<?php echo $titleClass; ?> text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl 2xl:text-5xl font-bold leading-tight"><?php echo $title; ?></h3>
            <p class="self-stretch <?php echo $textClass; ?> text-[11px] sm:text-xs md:text-sm lg:text-base xl:text-lg 2xl:text-xl font-normal leading-4 sm:leading-5 md:leading-6 lg:leading-7"><?php echo $description; ?></p>

            <a href="/<?php echo $slug; ?>"
               class="p-1 sm:p-1.5 md:p-2 lg:p-2.5 xl:p-3 2xl:p-3.5 <?php echo $buttonBg; ?> hover:bg-slate-800
                      rounded-md sm:rounded-lg md:rounded-xl lg:rounded-2xl
                      <?php echo $buttonBorder; ?>
                      inline-flex justify-center items-center transition-colors duration-200 group
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2">
                <span class="text-center <?php echo $buttonTextClass; ?> group-hover:text-stone-100 text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-normal transition-colors duration-200">
                    <?php echo $buttonLabel; ?>
                </span>
            </a>
        </div>

        <figure class="w-full lg:flex-1 order-1 lg:order-2">
            <img class="w-full h-[140px] sm:h-[180px] md:h-[240px] lg:h-[220px] xl:h-[300px] 2xl:h-[400px] object-cover rounded-[10px] sm:rounded-[15px] md:rounded-[25px] lg:rounded-[40px]" src="<?php echo $imageSrc; ?>" alt="<?php echo $imageAlt; ?>">
        </figure>
    <?php endif; ?>
</article>
