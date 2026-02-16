<?php
/**
 * Restaurant Cards section partial.
 * Displays participating restaurants with filters and card grid.
 * Button colors now match hero section (red/royal-blue).
 *
 * Restaurant-only section.
 *
 * Optional variable:
 * @var array|null $restaurantCardsSection
 */

$title = $restaurantCardsSection['title'] ?? 'Explore the participant restaurants';
$subtitle = $restaurantCardsSection['subtitle'] ?? 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.';
$filters = $restaurantCardsSection['filters'] ?? ['All', 'Dutch', 'European', 'French', 'Modern', 'Fish & Seafood', 'Vegetarian'];
$cards = $restaurantCardsSection['cards'] ?? null;
?>

<section id="restaurants-grid" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 flex flex-col justify-start items-start gap-6 sm:gap-8 md:gap-10">

    <!-- Section Header -->
    <div class="self-stretch flex flex-col justify-start items-start gap-4 sm:gap-6">
        <h2 class="text-gray-900 text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
            <?= nl2br(htmlspecialchars((string)$title)) ?>
        </h2>
        <p class="text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed">
            <?= nl2br(htmlspecialchars((string)$subtitle)) ?>
        </p>
    </div>

    <!-- Filter Section -->
    <div class="self-stretch p-4 sm:p-6 bg-slate-800 rounded-2xl sm:rounded-3xl flex flex-col sm:flex-row justify-start items-start sm:items-center gap-4 sm:gap-6 overflow-x-auto">
        <div class="flex justify-start items-center gap-2.5">
            <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <line x1="4" y1="6" x2="20" y2="6"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
            <span class="text-white text-lg sm:text-xl font-medium font-['Montserrat'] whitespace-nowrap">Filters</span>
        </div>

        <div class="flex justify-start items-center gap-2 sm:gap-3 overflow-x-auto flex-shrink-0">
            <?php foreach ($filters as $idx => $filterLabel): ?>
                <?php $label = (string)$filterLabel; ?>
                <?php if ($idx === 0): ?>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-red hover:bg-royal-blue rounded-lg sm:rounded-xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <?= htmlspecialchars($label) ?>
                    </button>
                <?php else: ?>
                    <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-stone-100 hover:bg-red rounded-lg sm:rounded-xl text-slate-800 hover:text-white text-lg sm:text-xl font-normal font-['Montserrat'] whitespace-nowrap transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                        <?= htmlspecialchars($label) ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (is_array($cards) && $cards !== []): ?>
        <!-- Restaurant Cards Grid (CMS-driven) -->
        <div class="self-stretch grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($cards as $index => $card): ?>
                <?php
                    $name = (string)($card['name'] ?? '');
                $cuisine = (string)($card['cuisine'] ?? '');
                $address = (string)($card['address'] ?? '');
                $description = (string)($card['description'] ?? '');
                $distanceText = (string)($card['distanceText'] ?? '');
                $rating = (int)($card['rating'] ?? 0);
                $price = (string)($card['price'] ?? '');
                $image = (string)($card['image'] ?? '');
                $aboutLabel = (string)($card['aboutLabel'] ?? 'About it');
                $bookLabel = (string)($card['bookLabel'] ?? 'Book table');
                $isNewVegas = (stripos($name, 'new vegas') !== false);
                ?>

                <div class="bg-white rounded-3xl outline outline-2 outline-slate-800 overflow-hidden flex flex-col h-full">
                    <?php if ($isNewVegas): ?>
                        <div class="w-full h-48 sm:h-60 p-2.5 flex justify-end items-start" style="background-image: url('<?= htmlspecialchars($image) ?>'); background-size: cover; background-position: center;">
                            <div class="px-3 py-2 bg-stone-100 rounded-lg flex justify-center items-center">
                                <!-- Existing vegan icon from our current UI (inline svg) -->
                                <svg class="w-7 h-7" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M15 1.2737e-10C11.0218 -1.6393e-05 7.20645 1.58236 4.39339 4.39902C1.58034 7.21568 -1.614e-05 11.0359 1.23629e-10 15.0193C0.000188882 22.7357 5.84023 29.1965 13.5086 29.9637C13.4878 29.4853 13.4564 28.9733 13.4071 28.6122C12.8042 24.1881 11.9475 19.7609 10.505 15.5359C8.62258 10.0224 4.63161 4.53513 4.63161 4.53513C4.63161 4.53513 6.59687 5.39103 7.47651 6.10613C8.80021 7.18223 9.98985 8.86111 10.8993 10.3392C13.3782 14.3676 15.1346 23.0772 15.1346 23.0772C15.1346 23.0772 17.0807 17.7877 18.3132 15.2591C19.4082 13.0123 20.9612 10.8093 22.191 8.80448C21.4552 8.71317 20.8195 9.31694 20.2109 9.8269C19.3763 10.5263 18.0569 12.8341 18.0569 12.8341C18.0569 12.8341 18.2356 10.1567 18.5372 9.28385C18.9802 8.00186 19.8905 6.90043 20.9354 6.35422C21.685 5.96237 25.1036 5.36932 26.2996 5.16993C23.4563 1.89246 19.3358 0.00717447 15 1.2737e-10ZM27.7492 7.12614C27.5525 8.63868 27.1757 10.6795 26.4988 11.6984C26.0445 12.3822 24.8151 12.9634 24.0755 13.3184C22.8815 13.8915 21.0366 13.9732 21.0366 13.9732C21.0366 13.9732 19.2141 17.2879 18.5895 19.0644C17.3657 22.5448 16.527 26.3728 16.0859 29.8464C16.0794 29.8977 16.0735 29.9488 16.0675 30C23.9174 29.4392 29.9998 22.8993 30 15.0193C29.9963 12.23 29.2169 9.49687 27.7492 7.12614Z" fill="#1A2A40"/>
                                </svg>
                            </div>
                        </div>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" class="w-full h-48 sm:h-60 object-cover p-2.5"/>
                    <?php endif; ?>

                    <div class="w-full h-1 bg-stone-300 border-b-2 border-slate-800"></div>

                    <div class="flex-1 p-4 sm:p-5 flex flex-col justify-start gap-3 sm:gap-4">
                        <div class="flex justify-between items-start gap-4">
                            <h3 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']"><?= htmlspecialchars($name) ?></h3>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex gap-1">
                                    <?php for ($s = 0; $s < max(0, min(5, $rating)); $s++): ?>
                                        <svg class="w-5 h-5 text-amber-400 fill-amber-400" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($price !== ''): ?>
                                    <span class="text-slate-800 text-base font-semibold font-['Montserrat']"><?= htmlspecialchars($price) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <?php if ($cuisine !== ''): ?>
                                <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Cuisine:</span> <?= htmlspecialchars($cuisine) ?></p>
                            <?php endif; ?>
                            <?php if ($address !== ''): ?>
                                <p class="text-slate-800 text-sm sm:text-base font-['Montserrat']"><span class="font-bold">Address:</span> <?= htmlspecialchars($address) ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if ($description !== ''): ?>
                            <p class="text-slate-800 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                                <?= htmlspecialchars($description) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($distanceText !== ''): ?>
                            <div class="flex items-center gap-2 text-gray-700 text-sm sm:text-base font-normal font-['Montserrat']">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M3.33289 13.3346V11.3513C3.33289 9.58464 2.47456 8.7513 2.49956 6.66797C2.52456 4.4013 3.74123 1.66797 6.24956 1.66797C7.80789 1.66797 8.33289 3.16797 8.33289 4.58464C8.33289 7.1763 6.66623 9.3013 6.66623 11.818V13.3346C6.66623 13.7767 6.49063 14.2006 6.17807 14.5131C5.86551 14.8257 5.44159 15.0013 4.99956 15.0013C4.55753 15.0013 4.13361 14.8257 3.82105 14.5131C3.50849 14.2006 3.33289 13.7767 3.33289 13.3346Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16.667 16.6667V14.6833C16.667 12.9167 17.5253 12.0833 17.5003 10C17.4753 7.73333 16.2587 5 13.7503 5C12.192 5 11.667 6.5 11.667 7.91667C11.667 10.5083 13.3337 12.6333 13.3337 15.15V16.6667C13.3337 17.1087 13.5093 17.5326 13.8218 17.8452C14.1344 18.1577 14.5583 18.3333 15.0003 18.3333C15.4424 18.3333 15.8663 18.1577 16.1788 17.8452C16.4914 17.5326 16.667 17.1087 16.667 16.6667Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.333 14.168H16.6663" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3.33301 10.832H6.66634" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span><?= htmlspecialchars($distanceText) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex gap-3 mt-auto pt-4 justify-center w-full">
                            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-red hover:bg-royal-blue rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                                <?= htmlspecialchars($aboutLabel) ?>
                            </button>
                            <button class="px-4 sm:px-5 py-2.5 sm:py-3 bg-red hover:bg-royal-blue rounded-2xl text-white text-lg sm:text-xl font-normal font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                                <?= htmlspecialchars($bookLabel) ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- No cards available (CMS-only mode) -->
    <?php endif; ?>

</section>

