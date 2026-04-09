<?php
/**
 * @var \App\ViewModels\HomeExploreBannerViewModel $exploreBanner
 */

use App\Helpers\CmsOutputHelper;

?>

<!-- Explore Banner -->
<section class="w-full min-h-[280px] sm:min-h-[350px] md:min-h-[500px] lg:min-h-[650px] xl:min-h-[760px] px-2 sm:px-4 md:px-8 lg:px-16 xl:px-24 py-4 sm:py-6 md:py-10 lg:py-12 flex flex-col justify-center items-center gap-2.5" aria-labelledby="explore-banner-heading">
    <div class="self-stretch flex-1 px-3 sm:px-4 md:px-8 lg:px-12 py-4 sm:py-5 md:py-6 lg:py-7 rounded-[15px] sm:rounded-[20px] md:rounded-[30px] lg:rounded-[40px] flex flex-col justify-end items-start gap-2 sm:gap-3 md:gap-4 lg:gap-5 bg-dynamic-fade-strong"
         style="--bg-url: url('<?= htmlspecialchars($exploreBanner->backgroundImageUrl) ?>')"
         role="img" aria-label="Panoramic view of Haarlem cityscape">
        <h2 id="explore-banner-heading" class="self-stretch text-white text-xl sm:text-2xl md:text-4xl lg:text-5xl xl:text-6xl font-bold"><?= CmsOutputHelper::text($exploreBanner->title) ?></h2>
        <p class="self-stretch text-white text-sm sm:text-base md:text-xl lg:text-2xl xl:text-3xl font-bold"><?= CmsOutputHelper::text($exploreBanner->subtitle) ?></p>
    </div>
</section>
