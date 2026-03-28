<?php
/**
 * Renders the main content sections for a storytelling event detail page:
 * about (text + images), highlights (feature cards), gallery, and video.
 * The reason for this is because these four sections form the editorial body of the
 * detail page and are kept together as one partial to separate content from
 * the hero and schedule sections that surround them.
 *
 * @var \App\ViewModels\Storytelling\StorytellingDetailPageViewModel $viewModel
 */

use App\View\ViewRenderer;

$about = $viewModel->aboutSection;
$highlights = $viewModel->highlightsSection;
$gallery = $viewModel->gallerySection;
$video = $viewModel->videoSection;
?>

<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                flex flex-col lg:flex-row justify-center items-center gap-8 md:gap-10 lg:gap-12"
         aria-labelledby="about-heading">
    <div class="w-full lg:flex-1 flex flex-col sm:flex-row gap-4 md:gap-6 lg:gap-8">
        <img src="<?= htmlspecialchars($about->image1Url) ?>"
             alt="<?= htmlspecialchars($about->heading) ?>"
             class="flex-1 w-full sm:w-1/2 aspect-square object-cover rounded-2xl">
        <img src="<?= htmlspecialchars($about->image2Url) ?>"
             alt="<?= htmlspecialchars($about->heading) ?>"
             class="flex-1 w-full sm:w-1/2 aspect-square object-cover rounded-2xl">
    </div>
    <div class="w-full lg:flex-1 flex flex-col gap-3 sm:gap-4 md:gap-5">
        <h2 id="about-heading"
            class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
            <?= htmlspecialchars($about->heading) ?>
        </h2>
        <div class="text-royal-blue text-base sm:text-lg md:text-xl font-normal leading-7 sm:leading-8">
            <?= $about->bodyHtml /* trusted CMS HTML — sanitised at input */ ?>
        </div>
    </div>
</section>

<?php if ($highlights->items !== []): ?>
    <section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                    flex flex-col gap-5 sm:gap-6 md:gap-8"
             aria-labelledby="highlights-heading">
        <h2 id="highlights-heading"
            class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
            <?= htmlspecialchars($highlights->heading) ?>
        </h2>
        <div class="w-full flex flex-col lg:flex-row justify-center items-stretch gap-6 md:gap-8 lg:gap-12">
            <?php foreach ($highlights->items as $highlight): ?>
                <article class="flex-1 bg-white rounded-2xl shadow-[0px_0px_24px_-2px_rgba(0,0,0,0.25)] inline-flex flex-col justify-start items-start overflow-hidden">
                    <img src="<?= htmlspecialchars($highlight->imageUrl) ?>"
                         alt="<?= htmlspecialchars($highlight->title) ?>"
                         class="w-full h-64 sm:h-72 md:h-80 lg:h-96 object-cover">
                    <div class="flex-1 w-full p-3.5 flex flex-col justify-start items-start gap-5 overflow-hidden">
                        <h3 class="text-royal-blue text-lg sm:text-xl md:text-2xl font-semibold">
                            <?= htmlspecialchars($highlight->title) ?>
                        </h3>
                        <p class="text-royal-blue text-base sm:text-lg md:text-xl font-normal">
                            <?= htmlspecialchars($highlight->description) ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                flex flex-col gap-5 sm:gap-6 md:gap-8"
         aria-labelledby="gallery-heading">
    <h2 id="gallery-heading"
        class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
        <?= htmlspecialchars($gallery->heading) ?>
    </h2>
    <div class="w-full flex flex-col gap-4 md:gap-6 lg:gap-12">
        <div class="w-full grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 lg:gap-12 h-auto sm:h-72 md:h-80 lg:h-96">
            <?php foreach ($gallery->topRowImages as $imgUrl): ?>
                <div class="rounded-3xl overflow-hidden h-48 sm:h-full">
                    <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($gallery->heading) ?>"
                         class="w-full h-full object-cover">
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($gallery->bottomRowImages !== []): ?>
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 lg:gap-12 h-auto sm:h-[300px] md:h-[400px] lg:h-[500px]">
                <?php foreach ($gallery->bottomRowImages as $imgUrl): ?>
                    <div class="rounded-3xl overflow-hidden h-48 sm:h-full">
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($gallery->heading) ?>"
                             class="w-full h-full object-cover">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-10 md:py-12
                flex flex-col gap-5 sm:gap-6 md:gap-8"
         aria-labelledby="video-heading">
    <h2 id="video-heading"
        class="text-royal-blue text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">
        <?= htmlspecialchars($video->heading) ?>
    </h2>
    <div class="w-full rounded-2xl overflow-hidden bg-royal-blue/10 aspect-video flex items-center justify-center">
        <?php if ($video->url !== ''): ?>
            <iframe
                src="<?= htmlspecialchars($video->url) ?>"
                class="w-full h-full"
                title="<?= htmlspecialchars($video->heading) ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy">
            </iframe>
        <?php else: ?>
            <div class="w-full h-full min-h-[300px] sm:min-h-[400px] lg:min-h-[600px]
                        bg-royal-blue/10 rounded-2xl flex flex-col items-center justify-center gap-4 p-8">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-royal-blue/20 flex items-center justify-center">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-royal-blue" viewBox="0 0 24 24"
                         fill="currentColor" aria-hidden="true">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <p class="text-royal-blue/60 text-lg font-normal text-center"><?= htmlspecialchars($video->placeholderText) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Social sharing buttons — placed below the video for discoverability
    ViewRenderer::render(__DIR__ . '/../sections/_social-share.php', [
        'shareUrl' => $viewModel->shareUrl,
        'shareTitle' => $viewModel->detailHero->title,
    ]);
    ?>
</section>
