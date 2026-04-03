<?php
/**
 * Reservation form section.
 *
 * @var \App\ViewModels\Restaurant\RestaurantDetailViewModel $viewModel
 */

$e = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

// Read session flash data (set by submitReservation on validation failure).
$formErrors  = $_SESSION['reservation_errors']    ?? [];
$oldInput    = $_SESSION['reservation_old_input'] ?? [];
unset($_SESSION['reservation_errors'], $_SESSION['reservation_old_input']);

$success = isset($_GET['success']);

$festivalDates  = $viewModel->reservation->festivalDates;
$reservationFee = $viewModel->reservation->reservationFeePerPerson;
?>

<section id="reservation-form" class="px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-10 sm:py-16 bg-white">
    <div class="max-w-5xl mx-auto flex flex-col gap-10">

        <!-- Back to restaurant link -->
        <a href="/restaurant/<?= $viewModel->slug ?>"
           class="inline-flex items-center gap-2 text-slate-800 hover:text-red font-['Montserrat'] font-medium transition-colors duration-200">
            <img src="/assets/Icons/Restaurant/arrow-left-icon.svg" alt="Back arrow" class="w-5 h-5" aria-hidden="true">
            Back to <?= $e($viewModel->name) ?>
        </a>

        <!-- Page heading -->
        <div class="flex flex-col gap-2">
            <h1 class="text-slate-800 text-4xl sm:text-5xl lg:text-6xl font-bold font-['Montserrat']"><?= $e($viewModel->reservation->labelTitle) ?></h1>
            <p class="text-slate-800 text-lg sm:text-xl font-normal font-['Montserrat']"><?= $e($viewModel->reservation->labelDesc) ?></p>
        </div>

        <!-- Success banner -->
        <?php if ($success): ?>
            <div class="p-5 bg-green-100 border border-green-400 rounded-xl text-green-800 text-lg font-['Montserrat']">
                <?= $e($viewModel->reservation->labelSuccess) ?>
            </div>
        <?php endif; ?>

        <!-- Error banner -->
        <?php if ($formErrors !== []): ?>
            <div class="p-5 bg-red-100 border border-red-400 rounded-xl text-red-800 font-['Montserrat']">
                <p class="font-bold mb-2"><?= $e($viewModel->reservation->labelErrorHeading) ?></p>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($formErrors as $err): ?>
                        <li><?= $e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Info cards: same style for all four -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

            <?php foreach ($viewModel->reservation->priceCards as $pc): ?>
                <div class="px-4 py-5 bg-stone-100 rounded-lg flex flex-col items-center gap-2 text-center">
                    <img src="/assets/Icons/Restaurant/person-icon.svg" alt="Person icon" class="w-8 h-8" aria-hidden="true">
                    <span class="text-slate-800 text-sm font-light font-['Montserrat']"><?= $e($pc['label']) ?></span>
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($pc['price']) ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ($viewModel->reservation->durationFormatted !== ''): ?>
                <div class="px-4 py-5 bg-stone-100 rounded-lg flex flex-col items-center gap-2 text-center">
                    <img src="/assets/Icons/Restaurant/clock-icon.svg" alt="Clock icon" class="w-8 h-8" aria-hidden="true">
                    <span class="text-slate-800 text-sm font-light font-['Montserrat']"><?= $e($viewModel->reservation->labelDuration) ?></span>
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->reservation->durationFormatted) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($viewModel->reservation->seatsFormatted !== ''): ?>
                <div class="px-4 py-5 bg-stone-100 rounded-lg flex flex-col items-center gap-2 text-center">
                    <img src="/assets/Icons/Restaurant/people-icon.svg" alt="People icon" class="w-8 h-8" aria-hidden="true">
                    <span class="text-slate-800 text-sm font-light font-['Montserrat']"><?= $e($viewModel->reservation->labelSeats) ?></span>
                    <span class="text-slate-800 text-lg font-bold font-['Montserrat']"><?= $e($viewModel->reservation->seatsFormatted) ?></span>
                </div>
            <?php endif; ?>

        </div>

        <!-- Reservation form -->
        <form id="reservation-form-fields"
              method="POST"
              action="/restaurant/<?= $viewModel->slug ?>/reservation"
              class="flex flex-col gap-8">

            <!-- Date and Time row -->
            <div class="flex flex-col sm:flex-row gap-6 sm:gap-10">

                <!-- Date -->
                <div class="flex flex-col gap-2">
                    <label for="dining_date" class="flex items-center gap-2 text-slate-800 text-lg font-bold font-['Montserrat']">
                        <img src="/assets/Icons/Restaurant/calendar-icon.svg" alt="Calendar icon" class="w-5 h-5" aria-hidden="true">
                        <?= $e($viewModel->reservation->labelDate) ?>
                    </label>
                    <select id="dining_date" name="dining_date"
                            class="w-48 h-10 pl-3 pr-8 bg-stone-100 rounded border border-slate-800 text-slate-800 text-lg font-['Montserrat'] appearance-none focus:outline-none focus:ring-2 focus:ring-red">
                        <option value=""><?= $e($viewModel->reservation->labelSelectDay) ?></option>
                        <?php foreach ($festivalDates as $day): ?>
                            <option value="<?= $e($day) ?>"
                                <?= (($oldInput['dining_date'] ?? '') === $day) ? 'selected' : '' ?>>
                                <?= $e($day) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Time -->
                <div class="flex flex-col gap-2">
                    <label for="time_slot" class="flex items-center gap-2 text-slate-800 text-lg font-bold font-['Montserrat']">
                        <img src="/assets/Icons/Restaurant/clock-icon.svg" alt="Clock icon" class="w-5 h-5" aria-hidden="true">
                        <?= $e($viewModel->reservation->labelTime) ?>
                    </label>
                    <select id="time_slot" name="time_slot"
                            class="w-48 h-10 pl-3 pr-8 bg-stone-100 rounded border border-slate-800 text-slate-800 text-lg font-['Montserrat'] appearance-none focus:outline-none focus:ring-2 focus:ring-red">
                        <option value=""><?= $e($viewModel->reservation->labelSelectTime) ?></option>
                        <?php foreach ($viewModel->reservation->timeSlots as $slot): ?>
                            <option value="<?= $e($slot) ?>"
                                <?= (($oldInput['time_slot'] ?? '') === $slot) ? 'selected' : '' ?>>
                                <?= $e($slot) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Number of guests -->
            <div class="flex flex-col gap-4">
                <h2 class="text-slate-800 text-xl font-bold font-['Montserrat']"><?= $e($viewModel->reservation->labelGuestsTitle) ?></h2>

                <div class="flex flex-col sm:flex-row gap-6">

                    <!-- Adults -->
                    <div class="flex items-center gap-4">
                        <span class="w-28 text-slate-800 text-lg font-['Montserrat'] bg-stone-100 px-4 py-3 rounded text-center"><?= $e($viewModel->reservation->labelAdult) ?></span>
                        <div class="flex items-center gap-4 px-4 py-2 bg-stone-100 rounded-xl">
                            <button type="button" data-counter-target="adults_count" data-counter-action="decrease"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Decrease adults">
                                <span class="w-4 h-0.5 bg-stone-100 block"></span>
                            </button>
                            <span id="adults-display" class="text-slate-800 text-lg font-['Montserrat'] w-5 text-center">0</span>
                            <input type="hidden" name="adults_count" id="adults_count" value="<?= (int)($oldInput['adults_count'] ?? 0) ?>">
                            <button type="button" data-counter-target="adults_count" data-counter-action="increase"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Increase adults">
                                <svg class="w-4 h-4 text-stone-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Children -->
                    <div class="flex items-center gap-4">
                        <span class="w-28 text-slate-800 text-lg font-['Montserrat'] bg-stone-100 px-4 py-3 rounded text-center"><?= $e($viewModel->reservation->labelChildren) ?></span>
                        <div class="flex items-center gap-4 px-4 py-2 bg-stone-100 rounded-xl">
                            <button type="button" data-counter-target="children_count" data-counter-action="decrease"
                                    class="w-7 h-7 bg-slate-800 rounded-full flex items-center justify-center hover:bg-red transition-colors"
                                    aria-label="Decrease children">
                                <span class="w-4 h-0.5 bg-stone-100 block"></span>
                            </button>
                            <span id="children-display" class="text-slate-800 text-lg font-['Montserrat'] w-5 text-center">0</span>
                            <input type="hidden" name="children_count" id="children_count" value="<?= (int)($oldInput['children_count'] ?? 0) ?>">
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

            <!-- Special requests -->
            <div class="flex flex-col gap-2">
                <h2 class="text-slate-800 text-xl font-bold font-['Montserrat']"><?= $e($viewModel->reservation->labelSpecialRequests) ?></h2>
                <label for="special_requests" class="text-slate-800 text-base font-semibold font-['Montserrat']">
                    <?= $e($viewModel->reservation->labelSpecialRequestsDesc) ?>
                </label>
                <textarea id="special_requests" name="special_requests" rows="4"
                          placeholder="<?= $e($viewModel->reservation->labelSpecialRequestsNote) ?>"
                          class="w-full p-3 bg-stone-100 rounded border border-slate-800 text-slate-800 text-lg font-['Montserrat'] resize-none focus:outline-none focus:ring-2 focus:ring-red"><?= $e($oldInput['special_requests'] ?? '') ?></textarea>
            </div>

            <!-- Total to be paid -->
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-slate-800 text-xl font-bold font-['Montserrat']"><?= $e($viewModel->reservation->labelTotalTitle) ?></p>
                    <p class="text-slate-800 text-base font-['Montserrat'] mt-2">
                        <?= $e(str_replace('{fee}', '€' . number_format($reservationFee, 0), $viewModel->reservation->labelFeeNote)) ?>
                    </p>
                </div>

                <!-- Dynamic fee summary -->
                <div id="fee-summary" class="p-5 bg-stone-100 rounded-lg flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8">
                    <div id="fee-guests-label" class="text-slate-800 text-lg font-bold font-['Montserrat']">—</div>
                    <div id="fee-breakdown" class="text-slate-800 text-lg font-['Montserrat']">Select guests to see total</div>
                    <div id="fee-total" class="text-slate-800 text-2xl font-bold font-['Montserrat'] sm:ml-auto">€ 0.00</div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit"
                        class="px-6 py-3.5 bg-red hover:bg-royal-blue rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center justify-center gap-2">
                    <?= $e($viewModel->reservation->labelButton) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </button>
                <a href="/restaurant/<?= $viewModel->slug ?>"
                   class="px-6 py-3.5 bg-slate-800 hover:bg-slate-600 rounded-2xl text-white text-xl font-normal font-['Montserrat'] transition-colors duration-200 flex items-center justify-center gap-2">
                    <?= $e($viewModel->reservation->labelBack) ?>
                    <svg class="w-2 h-4" viewBox="0 0 6 12" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 1l4 5-4 5"></path>
                    </svg>
                </a>
            </div>

        </form>
    </div>
</section>

<script>
(function () {
    const reservationFee = <?= (float) $reservationFee ?>;
    // +/- counter buttons
    document.querySelectorAll('[data-counter-action]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.dataset.counterTarget;
            const action   = btn.dataset.counterAction;
            const input    = document.getElementById(targetId);
            const display  = document.getElementById(targetId.replace('_count', '') + '-display');

            let value = parseInt(input.value, 10) || 0;
            if (action === 'increase') value++;
            if (action === 'decrease' && value > 0) value--;

            input.value   = value;
            display.textContent = value;
            updateTotal();
        });
    });

    // Initialise display from old input (e.g. after a failed submission)
    function initDisplays() {
        ['adults', 'children'].forEach(function (type) {
            const input   = document.getElementById(type + '_count');
            const display = document.getElementById(type + '-display');
            if (input && display) display.textContent = input.value || '0';
        });
        updateTotal();
    }

    function updateTotal() {
        const adults   = parseInt(document.getElementById('adults_count').value, 10)   || 0;
        const children = parseInt(document.getElementById('children_count').value, 10) || 0;
        const total    = (adults + children) * reservationFee;

        const guestsLabel  = document.getElementById('fee-guests-label');
        const breakdown    = document.getElementById('fee-breakdown');
        const feeTotal     = document.getElementById('fee-total');

        if (adults + children === 0) {
            guestsLabel.textContent = '—';
            breakdown.textContent   = 'Select guests to see total';
            feeTotal.textContent    = '€ 0.00';
            return;
        }

        const parts = [];
        if (adults > 0)   parts.push(adults   + ' Adult'   + (adults   > 1 ? 's' : ''));
        if (children > 0) parts.push(children + ' Child'   + (children > 1 ? 'ren' : ''));

        guestsLabel.textContent = parts.join(' + ');
        breakdown.textContent   = '€' + reservationFee + ' reservation fee × ' + (adults + children) + ' people';
        feeTotal.textContent    = '€ ' + total.toFixed(2).replace('.', ',');
    }

    initDisplays();
})();
</script>