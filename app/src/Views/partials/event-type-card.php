<?php
/**
 * @var array $eventType
 * @var bool $imageFirst
 */

$darkBg = $eventType['darkBg'] ?? false;
$bgClass = $darkBg ? 'bg-slate-800' : 'bg-stone-100';
$textClass = $darkBg ? 'text-stone-100' : 'text-slate-800';
$titleClass = $darkBg ? 'text-stone-100' : 'text-slate-800 underline';
$buttonBg = 'bg-stone-100';
$buttonText = 'text-slate-800';
$buttonBorder = 'border border-slate-800 hover:border-[#F5F1EB]';

$title = htmlspecialchars($eventType['title']);
$description = htmlspecialchars($eventType['description']);
$button = htmlspecialchars($eventType['button']);
$slug = htmlspecialchars($eventType['slug']);

$imageMap = [
    'jazz' => 'Image (Jazz).png',
    'dance' => 'Image (Dance).png',
    'history' => 'Image (History).png',
    'restaurant' => 'Image (Yummy).png',
    'storytelling' => 'Image (Story).png',
];
$imageSrc = '/assets/Image/' . $imageMap[$slug];
?>

<div class="self-stretch p-4 md:p-6 lg:p-10 <?php echo $bgClass; ?> rounded-[20px] md:rounded-[40px] flex flex-col lg:flex-row justify-center items-center gap-6 lg:gap-12 overflow-hidden">
    <?php if ($imageFirst): ?>
        <img class="w-full lg:flex-1 h-[200px] sm:h-[280px] md:h-[350px] lg:h-[400px] object-cover rounded-[20px] md:rounded-[40px]" src="<?php echo $imageSrc; ?>" alt="<?php echo $title; ?>">

        <div class="w-full lg:flex-1 flex flex-col justify-center items-start gap-2.5">
            <h3 class="<?php echo $titleClass; ?> text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight"><?php echo $title; ?></h3>
            <p class="self-stretch <?php echo $textClass; ?> text-base md:text-lg font-normal leading-relaxed"><?php echo $description; ?></p>

            <a href="/<?php echo $slug; ?>"
               class="p-2.5 md:p-3.5 <?php echo $buttonBg; ?> hover:bg-slate-800
                      rounded-xl md:rounded-2xl
                      <?php echo $buttonBorder; ?>
                      inline-flex justify-center items-center transition-colors duration-200 group">
                <span class="text-center <?php echo $buttonText; ?> group-hover:text-stone-100 text-base md:text-lg font-normal transition-colors duration-200">
                    <?php echo $button; ?>
                </span>
            </a>
        </div>

    <?php else: ?>
        <div class="w-full lg:flex-1 flex flex-col justify-center items-start gap-2.5 order-2 lg:order-1">
            <h3 class="<?php echo $titleClass; ?> text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight"><?php echo $title; ?></h3>
            <p class="self-stretch <?php echo $textClass; ?> text-base md:text-lg font-normal leading-relaxed"><?php echo $description; ?></p>

            <a href="/<?php echo $slug; ?>"
               class="p-2.5 md:p-3.5 <?php echo $buttonBg; ?> hover:bg-slate-800
                      rounded-xl md:rounded-2xl
                      <?php echo $buttonBorder; ?>
                      inline-flex justify-center items-center transition-colors duration-200 group">
                <span class="text-center <?php echo $buttonText; ?> group-hover:text-stone-100 text-base md:text-lg font-normal transition-colors duration-200">
                    <?php echo $button; ?>
                </span>
            </a>
        </div>

        <img class="w-full lg:flex-1 h-[200px] sm:h-[280px] md:h-[350px] lg:h-[400px] object-cover rounded-[20px] md:rounded-[40px] order-1 lg:order-2" src="<?php echo $imageSrc; ?>" alt="<?php echo $title; ?>">
    <?php endif; ?>
</div>
