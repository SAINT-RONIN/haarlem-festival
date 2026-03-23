<?php
/**
 * Restaurant detail page content sections.
 *
 * All content comes from the RestaurantDetailViewModel (domain data).
 * Images use placeholder paths when no MediaAsset is linked yet.
 *
 * @var \App\ViewModels\Restaurant\RestaurantDetailViewModel $viewModel
 */

// Helper: escapes the image path for safe HTML output.
$img = function (string $path): string {
    return htmlspecialchars($path, ENT_QUOTES);
};

$e = function (string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
};
?>

<!-- ============================================= -->
<!-- SECTION 1: Contact & Practical Info Cards     -->
<!-- ============================================= -->
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-stretch gap-8 lg:gap-12">

        <!-- Contact Card -->
        <div class="flex-1 pb-6 bg-white rounded-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-6 bg-royal-blue">
                <h2 class="text-white text-2xl sm:text-3xl font-medium font-['Montserrat']"><?= $e($viewModel->labelContactTitle) ?></h2>
            </div>

            <!-- Address -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->labelAddress) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($viewModel->address) ?></span>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->labelContact) ?></span>
                    <?php if ($viewModel->phone !== ''): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">Phone: <?= $e($viewModel->phone) ?></span>
                    <?php endif; ?>
                    <?php if ($viewModel->email !== ''): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">E-mail: <?= $e($viewModel->email) ?></span>
                    <?php endif; ?>
                    <?php if ($viewModel->website !== ''): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">Website: <?= $e($viewModel->website) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Open Hours -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->labelOpenHours) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                        <?= $e(implode('  •  ', $viewModel->timeSlots)) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Practical Info Card -->
        <div class="flex-1 pb-6 bg-white rounded-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-6 bg-royal-blue">
                <h2 class="text-white text-2xl sm:text-3xl font-medium font-['Montserrat']"><?= $e($viewModel->labelPracticalTitle) ?></h2>
            </div>

            <!-- Price and Food -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <span class="text-slate-800 text-4xl font-normal font-['Montserrat']">&euro;</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->labelPriceFood) ?></span>
                    <?php foreach ($viewModel->priceCards as $pc): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($pc['label']) ?>: <?= $e($pc['price']) ?></span>
                    <?php endforeach; ?>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($viewModel->labelCuisineType) ?> <?= $e($viewModel->cuisine) ?></span>
                </div>
            </div>

            <!-- Restaurant Rating -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-amber-400 fill-amber-400" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->labelRating) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                        <?= $e($viewModel->labelFestivalRated) ?> <?= str_repeat('★', $viewModel->rating) ?>
                    </span>
                    <?php if ($viewModel->michelinStars > 0): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                            <?= $viewModel->michelinStars ?> <?= $e($viewModel->labelMichelin) ?><?= $viewModel->michelinStars > 1 ? 's' : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Special Requests -->
            <?php if ($viewModel->specialRequestsNote !== ''): ?>
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->labelSpecialRequests) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($viewModel->specialRequestsNote) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================= -->
<!-- SECTION 2: Restaurant Gallery                 -->
<!-- ============================================= -->
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col items-center gap-6">
        <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->labelGalleryTitle) ?></h2>
        <div class="w-full flex flex-col md:flex-row justify-center items-center gap-8 lg:gap-12">
            <?php foreach ($viewModel->galleryImages as $index => $galleryImage): ?>
                <img class="flex-1 min-w-0 h-64 sm:h-80 md:h-96 lg:h-[450px] rounded-2xl shadow-lg object-cover" src="<?= $img($galleryImage) ?>" alt="<?= $e($viewModel->name) ?> gallery photo <?= $index + 1 ?>"/>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================= -->
<!-- SECTION 3: About the Restaurant               -->
<!-- Note: aboutText contains <strong> HTML from DB -->
<!-- for bold formatting. Only admin can edit this. -->
<!-- ============================================= -->
<?php if ($viewModel->aboutText !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-5">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($viewModel->labelAboutPrefix) ?> <?= $e($viewModel->name) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8">
                <?= nl2br($viewModel->aboutText) ?>
            </div>
        </div>
        <div class="flex-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[500px] rounded-2xl object-cover" src="<?= $img($viewModel->aboutImage) ?>" alt="About <?= $e($viewModel->name) ?>"/>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 4: Chef & Philosophy                  -->
<!-- ============================================= -->
<?php if ($viewModel->chefName !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 order-2 lg:order-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[500px] rounded-2xl object-cover" src="<?= $img($viewModel->chefImage) ?>" alt="Chef <?= $e($viewModel->chefName) ?>"/>
        </div>
        <div class="flex-1 flex flex-col gap-5 order-1 lg:order-2">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($viewModel->labelChefTitle) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
                <?= nl2br($viewModel->chefText) ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 5: Menu Style                         -->
<!-- ============================================= -->
<?php if ($viewModel->menuDescription !== '' || !empty($viewModel->cuisineTags)): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-5">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($viewModel->labelMenuTitle) ?></h2>
            <?php if (!empty($viewModel->cuisineTags)): ?>
                <h3 class="text-slate-800 text-2xl sm:text-3xl font-bold font-['Montserrat']"><?= $e($viewModel->labelCuisineType) ?></h3>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($viewModel->cuisineTags as $tag): ?>
                        <span class="px-5 py-2.5 bg-royal-blue rounded-lg text-white text-xl sm:text-2xl font-bold font-['Montserrat']"><?= $e($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($viewModel->menuDescription !== ''): ?>
                <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
                    <?= $e($viewModel->menuDescription) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="flex-1 flex items-center justify-center gap-4 sm:gap-6 max-w-[45%]">
            <?php foreach ($viewModel->menuImages as $index => $menuImage): ?>
                <img class="flex-1 min-w-0 h-48 sm:h-56 lg:h-64 rounded-2xl object-cover" src="<?= $img($menuImage) ?>" alt="Menu dish <?= $index + 1 ?>"/>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 6: Location                           -->
<!-- ============================================= -->
<?php if ($viewModel->locationDescription !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-6">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->labelLocationTitle) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl lg:text-2xl font-normal font-['Montserrat'] leading-8">
                <?= nl2br($viewModel->locationDescription) ?>
                <br/><br/>
                <span class="font-bold"><?= $e($viewModel->labelLocationAddress) ?></span>: <?= $e($viewModel->address) ?>
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-4">
            <?php if ($viewModel->mapEmbedUrl !== ''): ?>
                <div class="w-full h-80 sm:h-96 lg:h-[450px] bg-white rounded-lg border-4 border-slate-800 overflow-hidden">
                    <iframe
                        class="w-full h-full"
                        src="<?= $e($viewModel->mapEmbedUrl) ?>"
                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?= $e($viewModel->name) ?> location on map">
                    </iframe>
                </div>
            <?php else: ?>
                <div class="w-full h-80 sm:h-96 lg:h-[450px] bg-gray-200 rounded-lg border-4 border-slate-800 flex items-center justify-center">
                    <span class="text-slate-500 text-xl font-['Montserrat']"><?= $e($viewModel->labelMapFallback) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 7: Make your Reservation              -->
<!-- ============================================= -->
<section id="reservation" class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">

        <!-- Reservation Info -->
        <div class="flex-1 flex flex-col gap-8">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->labelReservationTitle) ?></h2>
            <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
                <?= $e($viewModel->labelReservationDesc) ?>
            </p>

            <!-- Price & Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <?php foreach ($viewModel->priceCards as $pc): ?>
                    <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                        <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        <span class="text-slate-800 text-lg font-medium font-['Montserrat'] text-center"><?= $e($pc['label']) ?></span>
                        <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= $e($pc['price']) ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if ($viewModel->durationMinutes > 0): ?>
                <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                    <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($viewModel->labelDuration) ?></span>
                    <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= (int)($viewModel->durationMinutes / 60) ?> hours</span>
                </div>
                <?php endif; ?>

                <?php if ($viewModel->seatsPerSession > 0): ?>
                <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                    <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($viewModel->labelSeats) ?></span>
                    <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= $viewModel->seatsPerSession ?> per session</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Available Time Slots -->
            <?php if (!empty($viewModel->timeSlots)): ?>
            <div class="flex flex-col gap-4">
                <h3 class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($viewModel->labelSlotsLabel) ?></h3>
                <div class="grid grid-cols-3 gap-4 sm:gap-6 lg:gap-12">
                    <?php foreach ($viewModel->timeSlots as $slot): ?>
                        <div class="p-4 sm:p-6 bg-white rounded-lg border-2 sm:border-[3px] border-slate-800 flex justify-center items-center hover:bg-slate-100 transition-colors cursor-pointer">
                            <span class="text-slate-800 text-lg sm:text-xl font-medium font-['Montserrat']"><?= $e($slot) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <p class="text-center text-slate-800 text-lg font-normal font-['Montserrat']">
                <?= $e($viewModel->labelReservationNote) ?>
            </p>
            <div class="flex justify-center">
                <a href="/restaurant/<?= $viewModel->id ?>/reservation"
                   class="px-6 py-3.5 bg-red hover:bg-royal-blue rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center gap-2">
                    <?= $e($viewModel->labelReservationBtn) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Reservation Image -->
        <div class="flex-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[700px] rounded-2xl object-cover" src="<?= $img($viewModel->reservationImage) ?>" alt="<?= $e($viewModel->name) ?> reservation"/>
        </div>
    </div>
</section>
