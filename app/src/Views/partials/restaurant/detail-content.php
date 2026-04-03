<?php
/**
 * Restaurant detail page content sections.
 *
 * All content comes from the RestaurantDetailViewModel (domain data).
 * Images use placeholder paths when no MediaAsset is linked yet.
 *
 * @var \App\ViewModels\Restaurant\RestaurantDetailViewModel $viewModel
 */

$e = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>

<!-- ============================================= -->
<!-- SECTION 1: Contact & Practical Info Cards     -->
<!-- ============================================= -->
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-stretch gap-8 lg:gap-12">

        <!-- Contact Card -->
        <div class="flex-1 pb-6 bg-white rounded-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-6 bg-royal-blue">
                <h2 class="text-white text-2xl sm:text-3xl font-medium font-['Montserrat']"><?= $e($viewModel->contact->labelTitle) ?></h2>
            </div>

            <!-- Address -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <img src="/assets/Icons/Restaurant/location-icon.svg" alt="Location icon" class="w-8 h-8" aria-hidden="true">
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->contact->labelAddress) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($viewModel->contact->address) ?></span>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <img src="/assets/Icons/Restaurant/email-icon.svg" alt="Email icon" class="w-8 h-8" aria-hidden="true">
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->contact->labelContact) ?></span>
                    <?php if ($viewModel->contact->phone !== ''): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">Phone: <?= $e($viewModel->contact->phone) ?></span>
                    <?php endif; ?>
                    <?php if ($viewModel->contact->email !== ''): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">E-mail: <?= $e($viewModel->contact->email) ?></span>
                    <?php endif; ?>
                    <?php if ($viewModel->contact->website !== ''): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">Website: <?= $e($viewModel->contact->website) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Open Hours -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <img src="/assets/Icons/Restaurant/clock-icon.svg" alt="Clock icon" class="w-8 h-8" aria-hidden="true">
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->contact->labelOpenHours) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                        <?= $e(implode('  •  ', $viewModel->contact->timeSlots)) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Practical Info Card -->
        <div class="flex-1 pb-6 bg-white rounded-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-6 bg-royal-blue">
                <h2 class="text-white text-2xl sm:text-3xl font-medium font-['Montserrat']"><?= $e($viewModel->practicalInfo->labelTitle) ?></h2>
            </div>

            <!-- Price and Food -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <span class="text-slate-800 text-4xl font-normal font-['Montserrat']">&euro;</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->practicalInfo->labelPriceFood) ?></span>
                    <?php foreach ($viewModel->practicalInfo->priceCards as $pc): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($pc['label']) ?>: <?= $e($pc['price']) ?></span>
                    <?php endforeach; ?>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($viewModel->practicalInfo->labelCuisineType) ?> <?= $e($viewModel->practicalInfo->cuisine) ?></span>
                </div>
            </div>

            <!-- Restaurant Rating -->
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <img src="/assets/Icons/Restaurant/star-icon.svg" alt="Star icon" class="w-8 h-8" aria-hidden="true">
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->practicalInfo->labelRating) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                        <?= $e($viewModel->practicalInfo->labelFestivalRated) ?> <?= str_repeat('★', $viewModel->practicalInfo->rating) ?>
                    </span>
                    <?php if ($viewModel->practicalInfo->michelinStars > 0): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                            <?= $viewModel->practicalInfo->michelinStars ?> <?= $e($viewModel->practicalInfo->labelMichelin) ?><?= $viewModel->practicalInfo->michelinStars > 1 ? 's' : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Special Requests -->
            <?php if ($viewModel->practicalInfo->specialRequestsNote !== ''): ?>
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <img src="/assets/Icons/Restaurant/notes-icon.svg" alt="Notes icon" class="w-8 h-8" aria-hidden="true">
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->practicalInfo->labelSpecialRequests) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($viewModel->practicalInfo->specialRequestsNote) ?></span>
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
        <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->gallery->labelTitle) ?></h2>
        <div class="w-full flex flex-col md:flex-row justify-center items-center gap-8 lg:gap-12">
            <?php foreach ($viewModel->gallery->images as $index => $galleryImage): ?>
                <img class="flex-1 min-w-0 h-64 sm:h-80 md:h-96 lg:h-[450px] rounded-2xl shadow-lg object-cover" src="<?= $e($galleryImage) ?>" alt="<?= $e($viewModel->name) ?> gallery photo <?= $index + 1 ?>"/>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================= -->
<!-- SECTION 3: About the Restaurant               -->
<!-- Note: about->text may contain <strong> HTML   -->
<!-- from DB for bold formatting. Admin-only edit. -->
<!-- ============================================= -->
<?php if ($viewModel->about->text !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-5">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($viewModel->about->labelTitlePrefix) ?> <?= $e($viewModel->name) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8">
                <?= nl2br($viewModel->about->text) ?>
            </div>
        </div>
        <div class="flex-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[500px] rounded-2xl object-cover" src="<?= $e($viewModel->about->image) ?>" alt="About <?= $e($viewModel->name) ?>"/>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 4: Chef & Philosophy                  -->
<!-- ============================================= -->
<?php if ($viewModel->chef->name !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 order-2 lg:order-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[500px] rounded-2xl object-cover" src="<?= $e($viewModel->chef->image) ?>" alt="Chef <?= $e($viewModel->chef->name) ?>"/>
        </div>
        <div class="flex-1 flex flex-col gap-5 order-1 lg:order-2">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($viewModel->chef->labelTitle) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
                <?= nl2br($viewModel->chef->text) ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 5: Menu Style                         -->
<!-- ============================================= -->
<?php if ($viewModel->menu->description !== '' || !empty($viewModel->menu->cuisineTags)): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-5">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($viewModel->menu->labelTitle) ?></h2>
            <?php if (!empty($viewModel->menu->cuisineTags)): ?>
                <h3 class="text-slate-800 text-2xl sm:text-3xl font-bold font-['Montserrat']"><?= $e($viewModel->menu->labelCuisineType) ?></h3>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($viewModel->menu->cuisineTags as $tag): ?>
                        <span class="px-5 py-2.5 bg-royal-blue rounded-lg text-white text-xl sm:text-2xl font-bold font-['Montserrat']"><?= $e($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($viewModel->menu->description !== ''): ?>
                <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
                    <?= $e($viewModel->menu->description) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="flex-1 flex items-center justify-center gap-4 sm:gap-6 max-w-[45%]">
            <?php foreach ($viewModel->menu->images as $index => $menuImage): ?>
                <img class="flex-1 min-w-0 h-48 sm:h-56 lg:h-64 rounded-2xl object-cover" src="<?= $e($menuImage) ?>" alt="Menu dish <?= $index + 1 ?>"/>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================= -->
<!-- SECTION 6: Location                           -->
<!-- ============================================= -->
<?php if ($viewModel->location->description !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-6">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->location->labelTitle) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl lg:text-2xl font-normal font-['Montserrat'] leading-8">
                <?= nl2br($viewModel->location->description) ?>
                <br/><br/>
                <span class="font-bold"><?= $e($viewModel->location->labelAddress) ?></span>: <?= $e($viewModel->location->address) ?>
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-4">
            <?php if ($viewModel->location->mapEmbedUrl !== ''): ?>
                <div class="w-full h-80 sm:h-96 lg:h-[450px] bg-white rounded-lg border-4 border-slate-800 overflow-hidden">
                    <iframe
                        class="w-full h-full"
                        src="<?= $e($viewModel->location->mapEmbedUrl) ?>"
                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?= $e($viewModel->name) ?> location on map">
                    </iframe>
                </div>
            <?php else: ?>
                <div class="w-full h-80 sm:h-96 lg:h-[450px] bg-gray-200 rounded-lg border-4 border-slate-800 flex items-center justify-center">
                    <span class="text-slate-500 text-xl font-['Montserrat']"><?= $e($viewModel->location->labelMapFallback) ?></span>
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
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->reservation->labelTitle) ?></h2>
            <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-7">
                <?= $e($viewModel->reservation->labelDesc) ?>
            </p>

            <!-- Price & Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <?php foreach ($viewModel->reservation->priceCards as $pc): ?>
                    <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                        <img src="/assets/Icons/Restaurant/person-icon.svg" alt="Person icon" class="w-10 h-10" aria-hidden="true">
                        <span class="text-slate-800 text-lg font-medium font-['Montserrat'] text-center"><?= $e($pc['label']) ?></span>
                        <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= $e($pc['price']) ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if ($viewModel->reservation->durationMinutes > 0): ?>
                <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                    <img src="/assets/Icons/Restaurant/clock-icon.svg" alt="Clock icon" class="w-10 h-10" aria-hidden="true">
                    <span class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($viewModel->reservation->labelDuration) ?></span>
                    <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= (int)($viewModel->reservation->durationMinutes / 60) ?> hours</span>
                </div>
                <?php endif; ?>

                <?php if ($viewModel->reservation->seatsPerSession > 0): ?>
                <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                    <img src="/assets/Icons/Restaurant/people-icon.svg" alt="People icon" class="w-10 h-10" aria-hidden="true">
                    <span class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($viewModel->reservation->labelSeats) ?></span>
                    <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= $viewModel->reservation->seatsPerSession ?> per session</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Available Time Slots -->
            <?php if (!empty($viewModel->reservation->timeSlots)): ?>
            <div class="flex flex-col gap-4">
                <h3 class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($viewModel->reservation->labelSlots) ?></h3>
                <div class="grid grid-cols-3 gap-4 sm:gap-6 lg:gap-12">
                    <?php foreach ($viewModel->reservation->timeSlots as $slot): ?>
                        <div class="p-4 sm:p-6 bg-white rounded-lg border-2 sm:border-[3px] border-slate-800 flex justify-center items-center hover:bg-slate-100 transition-colors cursor-pointer">
                            <span class="text-slate-800 text-lg sm:text-xl font-medium font-['Montserrat']"><?= $e($slot) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <p class="text-center text-slate-800 text-lg font-normal font-['Montserrat']">
                <?= $e($viewModel->reservation->labelNote) ?>
            </p>
            <div class="flex justify-center">
                <a href="/restaurant/<?= $viewModel->slug ?>/reservation"
                   class="px-6 py-3.5 bg-red hover:bg-royal-blue rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center gap-2">
                    <?= $e($viewModel->reservation->labelButton) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Reservation Image -->
        <div class="flex-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[700px] rounded-2xl object-cover" src="<?= $e($viewModel->reservation->image) ?>" alt="<?= $e($viewModel->name) ?> reservation"/>
        </div>
    </div>
</section>