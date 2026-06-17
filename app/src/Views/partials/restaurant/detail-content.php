<?php
/**
 * Restaurant detail page content sections.
 *
 * @var \App\ViewModels\Restaurant\RestaurantDetailViewModel $viewModel
 */

$e = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$r = $viewModel->restaurant;
$labels = $viewModel->labels;
$label = static fn(string $key, string $default = ''): string => $labels[$key] ?? $default;
?>

<?php if ($r->addressLine !== null || $r->stars > 0): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-stretch gap-8 lg:gap-12">
        <?php if ($r->addressLine !== null): ?>
        <div class="flex-1 pb-6 bg-white rounded-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-6 bg-royal-blue">
                <h2 class="text-white text-2xl sm:text-3xl font-medium font-['Montserrat']"><?= $e($label('detail_contact_title', 'Contact')) ?></h2>
            </div>

            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($label('detail_label_address', 'Address')) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($r->fullAddress) ?></span>
                </div>
            </div>

            <?php if ($r->timeSlots !== []): ?>
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($label('detail_label_open_hours', 'Opening Hours')) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e(implode(' | ', $r->timeSlots)) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="flex-1 pb-6 bg-white rounded-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-6 bg-royal-blue">
                <h2 class="text-white text-2xl sm:text-3xl font-medium font-['Montserrat']"><?= $e($label('detail_practical_title', 'Practical Info')) ?></h2>
            </div>

            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($label('detail_label_price_food', 'PRICE AND FOOD')) ?></span>
                    <?php foreach ($viewModel->priceCards as $priceCard): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($priceCard['label']) ?>: <?= $e($priceCard['price']) ?></span>
                    <?php endforeach; ?>
                    <?php if ($r->cuisineType !== null): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($label('detail_menu_cuisine_label', 'Cuisine')) ?> <?= $e($r->cuisineType) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-amber-400 fill-amber-400" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($label('detail_label_rating', 'RESTAURANT RATING')) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($label('detail_label_festival_rated', 'Festival-rated')) ?></span>
                    <?php if ($r->stars > 0): ?>
                    <div class="flex items-center gap-1">
                        <?php for ($s = 0; $s < min(5, $r->stars); $s++): ?>
                            <svg class="h-5 w-5 fill-amber-400 text-amber-400" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($r->michelinStars > 0): ?>
                        <span class="text-slate-800 text-lg font-normal font-['Montserrat']">
                            <?= $r->michelinStars ?> <?= $e($label('detail_label_michelin', 'Michelin')) ?><?= $r->michelinStars > 1 ? 's' : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (($r->specialRequestsNote ?? '') !== ''): ?>
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($label('detail_label_special_requests', 'SPECIAL REQUESTS')) ?></span>
                    <span class="text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($r->specialRequestsNote) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($r->galleryImages !== []): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col items-center gap-6">
        <h2 class="self-stretch text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($label('detail_gallery_title', 'Gallery')) ?></h2>
        <div class="w-full flex flex-col md:flex-row justify-center items-center gap-8 lg:gap-12">
            <?php foreach ($r->galleryImages as $index => $galleryImage): ?>
                <img class="flex-1 min-w-0 h-64 sm:h-80 md:h-96 lg:h-[450px] rounded-2xl shadow-lg object-cover" src="<?= $e($galleryImage) ?>" alt="<?= $e($r->name) ?> gallery photo <?= $index + 1 ?>"/>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (($r->aboutText ?? '') !== '' || ($r->aboutImage ?? '') !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-5">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($label('detail_about_title_prefix', 'About')) ?> <?= $e($r->name) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8"><?= nl2br($e($r->aboutText ?? '')) ?></div>
        </div>
        <div class="flex-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[500px] rounded-2xl object-cover" src="<?= $e($r->aboutImage ?? '') ?>" alt="About <?= $e($r->name) ?>"/>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (($r->chefName ?? '') !== '' || ($r->chefText ?? '') !== '' || ($r->chefImage ?? '') !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 order-2 lg:order-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[500px] rounded-2xl object-cover" src="<?= $e($r->chefImage ?? '') ?>" alt="Chef <?= $e($r->chefName ?? '') ?>"/>
        </div>
        <div class="flex-1 flex flex-col gap-5 order-1 lg:order-2">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($label('detail_chef_title', 'The Chef')) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8"><?= nl2br($e($r->chefText ?? '')) ?></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (($r->menuDescription ?? '') !== '' || $r->cuisineTags !== [] || $r->menuImages !== []): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-5">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat'] leading-tight"><?= $e($label('detail_menu_title', 'Menu')) ?></h2>
            <?php if ($r->cuisineTags !== []): ?>
                <h3 class="text-slate-800 text-2xl sm:text-3xl font-bold font-['Montserrat']"><?= $e($label('detail_menu_cuisine_label', 'Cuisine')) ?></h3>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($r->cuisineTags as $tag): ?>
                        <span class="px-5 py-2.5 bg-royal-blue rounded-lg text-white text-xl sm:text-2xl font-bold font-['Montserrat']"><?= $e($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (($r->menuDescription ?? '') !== ''): ?>
                <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8"><?= $e($r->menuDescription) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($r->menuImages !== []): ?>
        <div class="flex-1 flex items-center justify-center gap-4 sm:gap-6 max-w-[45%]">
            <?php foreach ($r->menuImages as $index => $menuImage): ?>
                <img class="flex-1 min-w-0 h-48 sm:h-56 lg:h-64 rounded-2xl object-cover" src="<?= $e($menuImage) ?>" alt="Menu dish <?= $index + 1 ?>"/>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if (($r->locationDescription ?? '') !== '' || $r->fullAddress !== '' || ($r->mapEmbedUrl ?? '') !== ''): ?>
<section class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-6">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($label('detail_location_title', 'Location')) ?></h2>
            <div class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8">
                <?= nl2br($e($r->locationDescription ?? '')) ?>
                <br/><br/>
                <span class="font-bold"><?= $e($label('detail_location_address_label', 'Address')) ?></span>: <?= $e($r->fullAddress) ?>
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-4">
            <?php if (($r->mapEmbedUrl ?? '') !== ''): ?>
                <div class="w-full h-80 sm:h-96 lg:h-[450px] bg-white rounded-lg border-4 border-slate-800 overflow-hidden">
                    <iframe
                        class="w-full h-full map-embed-borderless"
                        src="<?= $e($r->mapEmbedUrl) ?>"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?= $e($r->name) ?> location on map">
                    </iframe>
                </div>
            <?php else: ?>
                <div class="w-full h-80 sm:h-96 lg:h-[450px] bg-gray-200 rounded-lg border-4 border-slate-800 flex items-center justify-center">
                    <span class="text-slate-500 text-xl font-['Montserrat']"><?= $e($label('detail_map_fallback_text', 'Map coming soon')) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section id="reservation" class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        <div class="flex-1 flex flex-col gap-8">
            <h2 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($label('detail_reservation_title', 'Make a Reservation')) ?></h2>
            <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat'] leading-8"><?= $e($label('detail_reservation_description', '')) ?></p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <?php foreach ($viewModel->priceCards as $priceCard): ?>
                    <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                        <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        <span class="text-slate-800 text-lg font-medium font-['Montserrat'] text-center"><?= $e($priceCard['label']) ?></span>
                        <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= $e($priceCard['price']) ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if ($r->durationMinutes > 0): ?>
                <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                    <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($label('detail_label_duration', 'Duration')) ?></span>
                    <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= (int) ($r->durationMinutes / 60) ?> <?= $e($label('detail_label_duration_unit', 'hours')) ?></span>
                </div>
                <?php endif; ?>

                <?php if ($r->seatsPerSession > 0): ?>
                <div class="px-4 py-5 bg-white rounded-lg flex flex-col items-center gap-3">
                    <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($label('detail_label_seats', 'Seats')) ?></span>
                    <span class="text-slate-800 text-xl font-medium font-['Montserrat']"><?= $r->seatsPerSession ?> <?= $e($label('detail_label_seats_unit', 'per session')) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($r->timeSlots !== []): ?>
            <div class="flex flex-col gap-4">
                <h3 class="text-slate-800 text-lg font-medium font-['Montserrat']"><?= $e($label('detail_reservation_slots_label', 'Available Time Slots')) ?></h3>
                <div class="grid grid-cols-3 gap-4 sm:gap-6 lg:gap-12">
                    <?php foreach ($r->timeSlots as $slot): ?>
                        <div class="p-4 sm:p-6 bg-white rounded-lg border-2 sm:border-[3px] border-slate-800 flex justify-center items-center hover:bg-slate-100 transition-colors cursor-pointer">
                            <span class="text-slate-800 text-lg sm:text-xl font-medium font-['Montserrat']"><?= $e($slot) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <p class="text-center text-slate-800 text-lg font-normal font-['Montserrat']"><?= $e($label('detail_reservation_note', '')) ?></p>
            <div class="flex justify-center">
                <a href="/restaurant/<?= $e($r->slug) ?>/reservation"
                   class="px-6 py-3.5 bg-red hover:bg-royal-blue rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center gap-2">
                    <?= $e($label('detail_reservation_btn', 'Book Now')) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center">
            <img class="w-full h-auto max-h-[700px] rounded-2xl object-cover" src="<?= $e(\App\Constants\RestaurantPageConstants::RESERVATION_IMAGE) ?>" alt="<?= $e($r->name) ?> reservation"/>
        </div>
    </div>
</section>
