<?php
/**
 * Dance schedule section.
 */
?>

<section id="dance-schedule" class="w-full bg-sand py-12">
    <div class="hf-container">
        <div class="flex items-start justify-between mb-8">
            <div>
                <h2 class="text-[64px] leading-none font-extrabold text-[#16233B]">
                    DANCE schedule <span class="text-[34px] align-top">2026</span>
                </h2>
            </div>

            <div class="pt-6 text-[28px] font-semibold text-[#16233B]">
                24 Events
            </div>
        </div>

        <div class="mb-6">
            <button class="inline-flex items-center gap-2 rounded-[12px] bg-[#16233B] px-5 py-3 text-white text-[18px] font-medium">
                Filters
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            <!-- Friday -->
            <div class="rounded-[20px] bg-[#E8E2D9] p-4">
                <h3 class="text-center text-[28px] font-bold text-[#16233B] border-b border-[#16233B] pb-2 mb-4">
                    Friday
                </h3>

                <?php
                $fridayEvents = [
                        ['title' => 'Nicky Romero / Afrojack', 'venue' => 'Lichtfabriek', 'date' => 'Friday, July 31', 'time' => '20:00', 'price' => '€ 75.00', 'tag' => 'Back2Back'],
                        ['title' => 'Tiësto', 'venue' => 'Slachthuis', 'date' => 'Friday, July 31', 'time' => '22:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                        ['title' => 'Hardwell', 'venue' => 'Jopenkerk', 'date' => 'Friday, July 31', 'time' => '23:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                        ['title' => 'Armin van Buuren', 'venue' => 'XO the Club', 'date' => 'Friday, July 31', 'time' => '22:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                        ['title' => 'Martin Garrix', 'venue' => 'Puncher comedy club', 'date' => 'Friday, July 31', 'time' => '22:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                ];
                ?>

                <div class="space-y-4">
                    <?php foreach ($fridayEvents as $event): ?>
                        <div class="rounded-[18px] bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="text-[18px] leading-tight font-bold text-[#16233B] max-w-[180px]">
                                    <?= htmlspecialchars($event['title']) ?>
                                </h4>
                                <span class="rounded-full bg-[#D94B6A] px-3 py-1 text-white text-[12px]">
                                    <?= htmlspecialchars($event['tag']) ?>
                                </span>
                            </div>

                            <div class="mt-3 space-y-1 text-[15px] text-[#6B7280]">
                                <p><?= htmlspecialchars($event['venue']) ?></p>
                                <p><?= htmlspecialchars($event['date']) ?></p>
                                <p><?= htmlspecialchars($event['time']) ?></p>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t pt-3">
                                <span class="text-[18px] text-[#16233B]"><?= htmlspecialchars($event['price']) ?></span>
                                <button class="rounded-[10px] bg-[#16233B] px-4 py-2 text-white text-[14px]">
                                    Add to program
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Saturday -->
            <div class="rounded-[20px] bg-[#E8E2D9] p-4">
                <h3 class="text-center text-[28px] font-bold text-[#16233B] border-b border-[#16233B] pb-2 mb-4">
                    Saturday
                </h3>

                <?php
                $saturdayEvents = [
                        ['title' => 'Hardwell / Martin Garrix / Armin van Buuren', 'venue' => 'Caprera Openluchttheater', 'date' => 'Saturday, August 1', 'time' => '14:00', 'price' => '€ 110.00', 'tag' => 'Club'],
                        ['title' => 'Afrojack', 'venue' => 'Jopenkerk', 'date' => 'Saturday, August 1', 'time' => '22:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                        ['title' => 'Tiësto', 'venue' => 'Lichtfabriek', 'date' => 'Saturday, August 1', 'time' => '21:00', 'price' => '€ 75.00', 'tag' => 'TiëstoWorld'],
                        ['title' => 'Nicky Romero', 'venue' => 'Slachthuis', 'date' => 'Saturday, August 1', 'time' => '23:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                ];
                ?>

                <div class="space-y-4">
                    <?php foreach ($saturdayEvents as $event): ?>
                        <div class="rounded-[18px] bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="text-[18px] leading-tight font-bold text-[#16233B] max-w-[180px]">
                                    <?= htmlspecialchars($event['title']) ?>
                                </h4>
                                <span class="rounded-full bg-[#D94B6A] px-3 py-1 text-white text-[12px]">
                                    <?= htmlspecialchars($event['tag']) ?>
                                </span>
                            </div>

                            <div class="mt-3 space-y-1 text-[15px] text-[#6B7280]">
                                <p><?= htmlspecialchars($event['venue']) ?></p>
                                <p><?= htmlspecialchars($event['date']) ?></p>
                                <p><?= htmlspecialchars($event['time']) ?></p>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t pt-3">
                                <span class="text-[18px] text-[#16233B]"><?= htmlspecialchars($event['price']) ?></span>
                                <button class="rounded-[10px] bg-[#16233B] px-4 py-2 text-white text-[14px]">
                                    Add to program
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sunday -->
            <div class="rounded-[20px] bg-[#E8E2D9] p-4">
                <h3 class="text-center text-[28px] font-bold text-[#16233B] border-b border-[#16233B] pb-2 mb-4">
                    Sunday
                </h3>

                <?php
                $sundayEvents = [
                        ['title' => 'Afrojack / Tiësto / Nicky Romero', 'venue' => 'Caprera Openluchttheater', 'date' => 'Sunday, August 2', 'time' => '14:00', 'price' => '€ 110.00', 'tag' => 'Back2Back'],
                        ['title' => 'Armin van Buuren', 'venue' => 'Jopenkerk', 'date' => 'Sunday, August 2', 'time' => '19:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                        ['title' => 'Hardwell', 'venue' => 'XO the Club', 'date' => 'Sunday, August 2', 'time' => '21:00', 'price' => '€ 90.00', 'tag' => 'Club'],
                        ['title' => 'Martin Garrix', 'venue' => 'Slachthuis', 'date' => 'Sunday, August 2', 'time' => '18:00', 'price' => '€ 60.00', 'tag' => 'Club'],
                ];
                ?>

                <div class="space-y-4">
                    <?php foreach ($sundayEvents as $event): ?>
                        <div class="rounded-[18px] bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="text-[18px] leading-tight font-bold text-[#16233B] max-w-[180px]">
                                    <?= htmlspecialchars($event['title']) ?>
                                </h4>
                                <span class="rounded-full bg-[#D94B6A] px-3 py-1 text-white text-[12px]">
                                    <?= htmlspecialchars($event['tag']) ?>
                                </span>
                            </div>

                            <div class="mt-3 space-y-1 text-[15px] text-[#6B7280]">
                                <p><?= htmlspecialchars($event['venue']) ?></p>
                                <p><?= htmlspecialchars($event['date']) ?></p>
                                <p><?= htmlspecialchars($event['time']) ?></p>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t pt-3">
                                <span class="text-[18px] text-[#16233B]"><?= htmlspecialchars($event['price']) ?></span>
                                <button class="rounded-[10px] bg-[#16233B] px-4 py-2 text-white text-[14px]">
                                    Add to program
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- All Access -->
            <div class="rounded-[20px] bg-[#E8E2D9] p-4">
                <h3 class="text-center text-[28px] font-bold text-[#16233B] border-b border-[#16233B] pb-2 mb-4">
                    All Access
                </h3>

                <?php
                $accessPasses = [
                        ['title' => 'Friday- All Day Access', 'date' => 'Sunday, July 28', 'time' => '15:00 - 16:00', 'price' => '€ 125.00'],
                        ['title' => 'Saturday- All Day Access', 'date' => 'Sunday, July 28', 'time' => '16:00 - 17:00', 'price' => '€ 150.00'],
                        ['title' => 'Sunday- All Day Access', 'date' => 'Sunday, July 28', 'time' => '17:00 - 18:00', 'price' => '€ 150.00'],
                        ['title' => 'All Weekend Access', 'date' => 'Sunday, July 28', 'time' => '18:00 - 19:00', 'price' => '€ 250.00'],
                ];
                ?>

                <div class="space-y-4">
                    <?php foreach ($accessPasses as $pass): ?>
                        <div class="rounded-[18px] bg-white p-4">
                            <h4 class="text-[18px] leading-tight font-bold text-[#16233B] max-w-[200px]">
                                <?= htmlspecialchars($pass['title']) ?>
                            </h4>

                            <div class="mt-3 space-y-1 text-[15px] text-[#6B7280]">
                                <p><?= htmlspecialchars($pass['date']) ?></p>
                                <p><?= htmlspecialchars($pass['time']) ?></p>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t pt-3">
                                <span class="text-[18px] text-[#16233B]"><?= htmlspecialchars($pass['price']) ?></span>
                                <button class="rounded-[10px] bg-[#16233B] px-4 py-2 text-white text-[14px]">
                                    Add to program
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>