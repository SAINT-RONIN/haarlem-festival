<?php
/**
 * @var array $cms
 */
$banner = $cms['banner_section'] ?? [];
?>

<div class="w-full py-8 md:py-12">
    <div class="hf-container">
        <div class="w-full min-h-[320px] md:min-h-[420px] lg:min-h-[520px] px-6 md:px-12 py-7 bg-gradient-to-b from-black/0 to-black/80 rounded-[30px] md:rounded-[40px] flex flex-col justify-end items-start gap-3 md:gap-5 overflow-hidden" style="background: linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0.8)), url('/assets/Image/explore-incoming-events.png') center/cover;">
            <h2 class="self-stretch text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold"><?= htmlspecialchars($banner['banner_main_title'] ?? 'Explore all upcoming events') ?></h2>
            <p class="self-stretch text-white text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold"><?= htmlspecialchars($banner['banner_subtitle'] ?? 'See every theme and activity happening during the festival weekend.') ?></p>
        </div>
    </div>
</div>