<?php
/**
 * @var array $cms
 */
$banner = $cms['banner_section'] ?? [];
?>

<div class="w-full py-8 md:py-12">
    <div class="hf-container">
        <div class="w-full min-h-[320px] md:min-h-[420px] lg:min-h-[520px] px-6 md:px-12 py-7 bg-gradient-to-b from-black/0 to-black/80 rounded-[30px] md:rounded-[40px] flex flex-col justify-end items-start gap-3 md:gap-5 overflow-hidden" style="background: linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0.8)), url('/assets/Image/explore-incoming-events.png') center/cover;">
            <h2 class="self-stretch text-white text-4xl sm:text-5xl md:text-6xl font-bold leading-tight"><?= htmlspecialchars($banner['banner_main_title']) ?></h2>
            <p class="self-stretch text-white text-xl sm:text-2xl md:text-3xl font-bold leading-snug"><?= htmlspecialchars($banner['banner_subtitle']) ?></p>
        </div>
    </div>
</div>