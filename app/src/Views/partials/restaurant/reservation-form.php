<?php
/**
 * Reservation form section.
 *
 * @var \App\ViewModels\Restaurant\RestaurantDetailViewModel $viewModel
 */

$e = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$r = $viewModel->restaurant;
$labels = $viewModel->labels;
$label = static fn(string $key, string $default = ''): string => $labels[$key] ?? $default;
?>

<section id="reservation-form" class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-10 sm:py-16 bg-white">
    <div class="max-w-5xl mx-auto flex flex-col gap-10">
        <a href="/restaurant/<?= $e($r->slug) ?>"
           class="inline-flex items-center gap-2 text-slate-800 hover:text-red font-['Montserrat'] font-medium transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            <?= $e($label('detail_form_back_to_prefix', 'Back to')) ?> <?= $e($r->name) ?>
        </a>

        <div class="flex flex-col gap-2">
            <h1 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($label('detail_reservation_title', 'Make your Reservation')) ?></h1>
            <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat']"><?= $e($label('detail_reservation_description', '')) ?></p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($viewModel->priceCards as $priceCard): ?>
                <div class="px-4 py-5 bg-stone-100 rounded-lg flex flex-col items-center gap-2 text-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                    <span class="text-slate-800 text-sm font-light font-['Montserrat']"><?= $e($priceCard['label']) ?></span>
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($priceCard['price']) ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ($r->durationMinutes > 0): ?>
                <div class="px-4 py-5 bg-stone-100 rounded-lg flex flex-col items-center gap-2 text-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-slate-800 text-sm font-light font-['Montserrat']"><?= $e($label('detail_label_duration', 'Duration')) ?></span>
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= (int) ($r->durationMinutes / 60) ?> <?= $e($label('detail_label_duration_unit', 'hours')) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($r->seatsPerSession > 0): ?>
                <div class="px-4 py-5 bg-stone-100 rounded-lg flex flex-col items-center gap-2 text-center">
                    <svg class="w-8 h-8 text-slate-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-slate-800 text-sm font-light font-['Montserrat']"><?= $e($label('detail_label_seats', 'Seats')) ?></span>
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $r->seatsPerSession ?> <?= $e($label('detail_label_seats_unit', 'per session')) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <form id="reservation-form-fields"
              method="POST"
              action="/restaurant/<?= $e($r->slug) ?>/reservation"
              class="flex flex-col gap-8">

            <div class="flex flex-col sm:flex-row gap-6 sm:gap-10">
                <div class="flex flex-col gap-2">
                    <label for="dining_date" class="flex items-center gap-2 text-slate-800 text-lg font-bold font-['Montserrat']">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                        </svg>
                        <?= $e($label('detail_form_label_date', 'Date')) ?>
                    </label>
                    <select id="dining_date" name="dining_date"
                            class="w-48 h-10 pl-3 pr-8 bg-stone-100 rounded border border-slate-800 text-slate-800 text-lg font-['Montserrat'] appearance-none focus:outline-none focus:ring-2 focus:ring-red">
                        <option value=""><?= $e($label('detail_form_placeholder_date', 'Select a day')) ?></option>
                        <?php foreach ($viewModel->validDates as $day): ?>
                            <option value="<?= $e($day) ?>"><?= date('l, j M Y', strtotime($day)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="time_slot" class="flex items-center gap-2 text-slate-800 text-lg font-bold font-['Montserrat']">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?= $e($label('detail_form_label_time', 'Time')) ?>
                    </label>
                    <select id="time_slot" name="time_slot"
                            class="w-48 h-10 pl-3 pr-8 bg-stone-100 rounded border border-slate-800 text-slate-800 text-lg font-['Montserrat'] appearance-none focus:outline-none focus:ring-2 focus:ring-red">
                        <option value=""><?= $e($label('detail_form_placeholder_time', 'Select a time')) ?></option>
                        <?php foreach ($r->timeSlots as $slot): ?>
                            <option value="<?= $e($slot) ?>"><?= $e($slot) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <h2 class="text-slate-800 text-xl font-bold font-['Montserrat']"><?= $e($label('detail_form_guests_title', 'Number of Guests')) ?></h2>

                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="flex items-center gap-4">
                        <span class="w-28 text-slate-800 text-lg font-['Montserrat'] bg-stone-100 px-4 py-3 rounded text-center"><?= $e($label('detail_form_label_adult', 'Adult')) ?></span>
                        <div class="flex items-center gap-4 px-4 py-2 bg-stone-100 rounded-xl">
                            <button type="button" data-counter-target="adults_count" data-counter-action="decrease"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Decrease adults">
                                <span class="w-4 h-0.5 bg-stone-100 block"></span>
                            </button>
                            <span id="adults-display" class="text-slate-800 text-lg font-['Montserrat'] w-5 text-center">0</span>
                            <input type="hidden" name="adults_count" id="adults_count" value="0">
                            <button type="button" data-counter-target="adults_count" data-counter-action="increase"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Increase adults">
                                <svg class="w-4 h-4 text-stone-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="w-28 text-slate-800 text-lg font-['Montserrat'] bg-stone-100 px-4 py-3 rounded text-center"><?= $e($label('detail_form_label_children', 'Children')) ?></span>
                        <div class="flex items-center gap-4 px-4 py-2 bg-stone-100 rounded-xl">
                            <button type="button" data-counter-target="children_count" data-counter-action="decrease"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Decrease children">
                                <span class="w-4 h-0.5 bg-stone-100 block"></span>
                            </button>
                            <span id="children-display" class="text-slate-800 text-lg font-['Montserrat'] w-5 text-center">0</span>
                            <input type="hidden" name="children_count" id="children_count" value="0">
                            <button type="button" data-counter-target="children_count" data-counter-action="increase"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Increase children">
                                <svg class="w-4 h-4 text-stone-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <h2 class="text-slate-800 text-xl font-bold font-['Montserrat']"><?= $e($label('detail_form_special_requests_title', 'Special requests')) ?></h2>
                <label for="special_requests" class="text-slate-800 text-base font-semibold font-['Montserrat']">
                    <?= $e($label('detail_form_special_requests_subtitle', 'Diet, allergies, accessibility needs')) ?>
                </label>
                <textarea id="special_requests" name="special_requests" rows="4"
                          placeholder="<?= $e($label('detail_form_special_requests_placeholder', 'Let us know if you have any special requirements')) ?>"
                          class="w-full p-3 bg-stone-100 rounded border border-slate-800 text-slate-800 text-lg font-['Montserrat'] resize-none focus:outline-none focus:ring-2 focus:ring-red"></textarea>
            </div>

            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-slate-800 text-xl font-bold font-['Montserrat']"><?= $e($label('detail_form_total_title', 'Total to be paid')) ?></p>
                    <p class="text-slate-800 text-base font-['Montserrat'] mt-2">
                        <?= $e(str_replace('{fee}', 'EUR ' . number_format($r->reservationFee, 0), $label('detail_reservation_fee_text', 'To complete your reservation, you pay a {fee} fee per person. This amount is deducted from your final bill at the restaurant, so you simply pay the remaining amount after your meal.'))) ?>
                    </p>
                </div>

                <div id="fee-summary" class="p-5 bg-stone-100 rounded-lg flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8">
                    <div id="fee-guests-label" class="text-slate-800 text-lg font-bold font-['Montserrat']">-</div>
                    <div id="fee-breakdown" class="text-slate-800 text-lg font-['Montserrat']">Select guests to see total</div>
                    <div id="fee-total" class="text-slate-800 text-2xl font-bold font-['Montserrat'] sm:ml-auto">EUR 0.00</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit"
                        class="px-6 py-3.5 bg-red hover:bg-royal-blue rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center justify-center gap-2">
                    <?= $e($label('detail_reservation_btn', 'Book Now')) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </button>
                <a href="/restaurant/<?= $e($r->slug) ?>"
                   class="px-6 py-3.5 bg-slate-800 hover:bg-slate-600 rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center justify-center gap-2">
                    <?= $e($label('detail_form_back_to_restaurant', 'Back to Restaurant')) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </a>
            </div>
        </form>
    </div>
</section>

<script>
window.reservationConfig = { reservationFee: <?= (float) $r->reservationFee ?> };
</script>
<script src="/assets/js/restaurant-reservation-form.js"></script>
