<?php
/**
 * @var array $cms
 */
$about = $cms['about_section'] ?? [];
?>

<div class="w-full py-8 md:py-12">
    <div class="hf-container flex flex-col lg:flex-row justify-center items-center gap-8 lg:gap-12 overflow-hidden">
        <div class="flex-1 flex flex-col justify-start items-start gap-4 md:gap-5">
            <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold leading-tight lg:leading-[70px]"><?= htmlspecialchars($about['about_main_title'] ?? 'What is Haarlem Festival?') ?></h2>
            <p class="self-stretch text-slate-800 text-lg md:text-xl lg:text-2xl font-semibold leading-6"><?= htmlspecialchars($about['about_tagline'] ?? 'A celebration of culture and creativity') ?></p>
            <div class="self-stretch text-slate-800 text-base md:text-lg lg:text-xl font-normal leading-6">
                <?= $about['about_description'] ?? '<p>Haarlem Festival is a four-day celebration that brings together the city\'s most inspiring music, stories, food, and cultural experiences.</p>' ?>
            </div>
            <a href="#schedule" class="w-full sm:w-80 px-8 md:px-12 py-3 md:py-3.5 bg-pink-700 hover:bg-[#1A2A40] rounded-xl md:rounded-2xl inline-flex justify-center items-center gap-2.5 transition-colors duration-200">
                <span class="text-white text-xl md:text-2xl font-normal leading-6"><?= htmlspecialchars($about['about_button'] ?? 'Events schedule') ?></span>
            </a>
        </div>
        <img class="w-full lg:flex-1 h-auto max-h-[400px] lg:max-h-[530px] rounded-2xl md:rounded-3xl object-cover" src="/assets/Image/what-is-haarlem.png" alt="Haarlem Festival">
    </div>
</div>
