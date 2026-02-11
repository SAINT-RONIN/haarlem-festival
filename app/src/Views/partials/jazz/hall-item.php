<?php
/**
 * Single hall item partial.
 *
 * @var \App\ViewModels\HallData $hall
 */
?>

<?php if ($hall->isFree): ?>
    <!-- Free Entry Hall -->
    <div class="self-stretch bg-gray-50 rounded-md shadow-md flex flex-col justify-start items-start">
        <div class="self-stretch p-2.5 rounded-t-md flex flex-col justify-start items-start">
            <div class="self-stretch flex flex-col justify-start items-start gap-3.5">
                <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">
                    <?= htmlspecialchars($hall->name) ?>
                </p>
                <p class="self-stretch text-royal-blue text-sm sm:text-base font-normal font-['Montserrat']">
                    <?= htmlspecialchars($hall->description) ?>
                </p>
            </div>
            <div class="self-stretch flex justify-end items-start gap-2 mt-2">
                <div class="p-1.5 bg-white rounded-md shadow-sm flex justify-center items-center gap-2.5">
                    <span class="text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-tight">€</span>
                    <span class="text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-tight"><?= htmlspecialchars($hall->price) ?></span>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Paid Hall -->
    <div class="self-stretch p-2.5 bg-gray-50 rounded-md shadow-md flex flex-col justify-start items-start">
        <div class="self-stretch flex flex-col justify-start items-start gap-3.5">
            <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">
                <?= htmlspecialchars($hall->name) ?>
            </p>
            <p class="self-stretch text-royal-blue text-base sm:text-lg font-normal font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($hall->description) ?>
            </p>
        </div>
        <div class="self-stretch flex justify-end items-end gap-2 mt-2">
            <img src="/assets/Icons/People-Icon.svg" alt="Seats" class="w-5 h-5" loading="lazy">
            <span class="text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-tight">
                <?= htmlspecialchars($hall->capacity) ?>
            </span>
        </div>
    </div>
<?php endif; ?>

