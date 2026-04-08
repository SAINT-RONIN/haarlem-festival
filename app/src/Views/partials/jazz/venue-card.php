<?php
/**
 * Single venue card partial.
 *
 * @var \App\ViewModels\VenueData $venue
 */

use App\View\ViewRenderer;

?>

<div class="flex-1 self-stretch pb-6 bg-white rounded-2xl flex flex-col justify-start items-start gap-6 overflow-hidden shadow-md">
    <!-- Header -->
    <div class="self-stretch p-6 bg-royal-blue flex justify-start items-start gap-2.5 shrink-0">
        <h3 class="flex-1 text-white text-2xl sm:text-3xl font-medium font-['Montserrat'] leading-tight">
            <?= htmlspecialchars($venue->name) ?>
        </h3>
    </div>

    <!-- Content -->
    <div class="self-stretch flex-1 px-6 flex flex-col justify-start items-start gap-6 sm:gap-7">
        <!-- Address / Location -->
        <?php if ($venue->addressLine1): ?>
            <div class="self-stretch flex flex-col justify-start items-start gap-4">
                <div class="self-stretch flex flex-col justify-center items-start gap-2">
                    <div class="flex items-center gap-2.5">
                        <img src="/assets/Icons/Location-Icon.svg" alt="Location icon" class="w-6 h-6" loading="lazy">
                        <p class="text-royal-blue text-lg sm:text-xl font-bold font-['Montserrat'] leading-5">
                            <?= $venue->contactInfo ? 'ADDRESS' : 'LOCATION' ?>
                        </p>
                    </div>
                    <div class="self-stretch flex flex-col justify-start items-start gap-2">
                        <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">
                            <?= htmlspecialchars($venue->addressLine1) ?>
                        </p>
                        <?php if ($venue->addressLine2): ?>
                            <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">
                                <?= htmlspecialchars($venue->addressLine2) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contact -->
        <?php if ($venue->contactInfo): ?>
            <div class="self-stretch flex flex-col justify-start items-start">
                <div class="self-stretch flex flex-col justify-center items-start gap-2">
                    <div class="flex items-center gap-2.5">
                        <img src="/assets/Icons/Mail-Icon.svg" alt="Mail icon" class="w-6 h-6" loading="lazy">
                        <p class="text-royal-blue text-lg sm:text-xl font-bold font-['Montserrat'] leading-5">
                            CONTACT
                        </p>
                    </div>
                    <div class="self-stretch flex flex-col justify-start items-start gap-2">
                        <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">
                            <?= htmlspecialchars($venue->contactInfo) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Halls -->
        <?php if (!empty($venue->halls)): ?>
            <div class="self-stretch pt-3.5 border-t border-gray-200 flex flex-col justify-start items-start gap-4">
                <p class="text-royal-blue text-base sm:text-lg font-normal font-['Montserrat']">
                    <?= count($venue->halls) > 1 ? 'AVAILABLE HALLS' : 'OUTDOOR STAGE' ?>
                </p>
                <div class="self-stretch flex flex-col justify-start items-start gap-3.5">
                    <?php foreach ($venue->halls as $hall): ?>
                        <?php ViewRenderer::render(__DIR__ . '/hall-item.php', ['hall' => $hall]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
