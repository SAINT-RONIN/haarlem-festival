<?php
/**
 * Restaurant Cards section partial.
 * Displays participating restaurants with filters and card grid.
 *
 * Expects a \App\ViewModels\Restaurant\RestaurantPageViewModel as $viewModel
 * and uses its restaurantCardsSection property.
 *
 * @var \App\ViewModels\Restaurant\RestaurantCardsSectionData $restaurantCardsSection
 */

$title        = $restaurantCardsSection->title;
$subtitle     = $restaurantCardsSection->subtitle;
$filters      = $restaurantCardsSection->filters;
$cards        = $restaurantCardsSection->cards;
$labelFilters = $restaurantCardsSection->labelFilters;
$labelAbout   = $restaurantCardsSection->labelAboutBtn;
$labelBook    = $restaurantCardsSection->labelBookBtn;
$activeFilter = $restaurantCardsSection->activeFilter;
?>

<section id="restaurants-grid" class="w-full px-4 py-12 sm:px-8 lg:px-14 lg:py-16 xl:px-20">
    <div class="mx-auto flex w-full max-w-[1520px] flex-col gap-8 lg:gap-10">
        <div class="flex w-full flex-col gap-3 sm:gap-4">
            <h2 class="text-3xl font-bold leading-tight text-slate-900 sm:text-4xl xl:text-[3.35rem]">
                <?= nl2br(htmlspecialchars($title)) ?>
            </h2>
            <p class="max-w-[1100px] text-sm leading-7 text-slate-500 sm:text-lg sm:leading-8">
                <?= nl2br(htmlspecialchars($subtitle)) ?>
            </p>
        </div>

        <div class="w-full rounded-[22px] bg-slate-800 px-5 py-4 sm:px-6 lg:px-7 lg:py-5"
             data-restaurant-filters="restaurant-cards">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:gap-5">
                <div class="flex items-center gap-3 text-white">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                    <span class="whitespace-nowrap text-base font-semibold font-['Montserrat'] sm:text-lg">
                        <?= htmlspecialchars($labelFilters) ?>
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5"
                     data-filter-group="cuisine" role="radiogroup" aria-label="Filter by cuisine">
                    <?php foreach ($filters as $idx => $label): ?>
                        <?php
                            $filterValue = ($idx === 0) ? 'all' : strtolower(trim($label));
                        $isActive = ($activeFilter === '' && $idx === 0) || ($activeFilter !== '' && $filterValue === $activeFilter);
                        ?>
                        <button type="button"
                                data-filter-value="<?= htmlspecialchars($filterValue) ?>"
                                role="radio"
                                aria-checked="<?= $isActive ? 'true' : 'false' ?>"
                                class="min-h-[44px] rounded-xl border border-white/10 px-4 py-2 text-sm font-medium font-['Montserrat'] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2 <?= $isActive ? 'bg-red text-white hover:bg-royal-blue' : 'bg-stone-100 text-slate-800 hover:bg-red hover:text-white' ?>">
                            <?= htmlspecialchars($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($cards !== []): ?>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <?php foreach ($cards as $card): ?>
                    <?php
                        $cuisineTags = array_values(array_filter(array_map(
                            static fn(string $tag): string => mb_strtolower(trim($tag)),
                            explode(',', $card->cuisine),
                        ), static fn(string $tag): bool => $tag !== ''));
                    ?>
                    <article class="flex h-full flex-col overflow-hidden rounded-[24px] border border-slate-400 bg-white shadow-[0_1px_0_rgba(15,23,42,0.08)]"
                             data-cuisines="<?= htmlspecialchars(implode('|', $cuisineTags)) ?>">
                        <div class="p-2 pb-0">
                            <?php if ($card->isVegan): ?>
                                <div class="flex h-[190px] w-full items-start justify-end rounded-[18px] bg-cover bg-center p-3 sm:h-[210px] xl:h-[175px] bg-dynamic"
                                     style="--bg-url: url('<?= htmlspecialchars($card->image) ?>')">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-stone-100/95 shadow-sm">
                                        <svg class="h-6 w-6" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M15 1.2737e-10C11.0218 -1.6393e-05 7.20645 1.58236 4.39339 4.39902C1.58034 7.21568 -1.614e-05 11.0359 1.23629e-10 15.0193C0.000188882 22.7357 5.84023 29.1965 13.5086 29.9637C13.4878 29.4853 13.4564 28.9733 13.4071 28.6122C12.8042 24.1881 11.9475 19.7609 10.505 15.5359C8.62258 10.0224 4.63161 4.53513 4.63161 4.53513C4.63161 4.53513 6.59687 5.39103 7.47651 6.10613C8.80021 7.18223 9.98985 8.86111 10.8993 10.3392C13.3782 14.3676 15.1346 23.0772 15.1346 23.0772C15.1346 23.0772 17.0807 17.7877 18.3132 15.2591C19.4082 13.0123 20.9612 10.8093 22.191 8.80448C21.4552 8.71317 20.8195 9.31694 20.2109 9.8269C19.3763 10.5263 18.0569 12.8341 18.0569 12.8341C18.0569 12.8341 18.2356 10.1567 18.5372 9.28385C18.9802 8.00186 19.8905 6.90043 20.9354 6.35422C21.685 5.96237 25.1036 5.36932 26.2996 5.16993C23.4563 1.89246 19.3358 0.00717447 15 1.2737e-10ZM27.7492 7.12614C27.5525 8.63868 27.1757 10.6795 26.4988 11.6984C26.0445 12.3822 24.8151 12.9634 24.0755 13.3184C22.8815 13.8915 21.0366 13.9732 21.0366 13.9732C21.0366 13.9732 19.2141 17.2879 18.5895 19.0644C17.3657 22.5448 16.527 26.3728 16.0859 29.8464C16.0794 29.8977 16.0735 29.9488 16.0675 30C23.9174 29.4392 29.9998 22.8993 30 15.0193C29.9963 12.23 29.2169 9.49687 27.7492 7.12614Z" fill="#1A2A40"/>
                                        </svg>
                                    </div>
                                </div>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($card->image) ?>"
                                     alt="<?= htmlspecialchars($card->name) ?>"
                                     class="h-[190px] w-full rounded-[18px] object-cover sm:h-[210px] xl:h-[175px]">
                            <?php endif; ?>
                        </div>

                        <div class="mt-2 border-t border-slate-300"></div>

                        <div class="flex h-full flex-col gap-3 px-4 pb-4 pt-4">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-[1.05rem] font-bold leading-[1.25] text-slate-800 sm:text-[1.15rem] xl:text-[1.08rem]">
                                    <?= htmlspecialchars($card->name) ?>
                                </h3>
                                <?php if ($card->rating > 0): ?>
                                    <div class="mt-0.5 flex shrink-0 items-center gap-1">
                                        <?php for ($s = 0; $s < max(0, min(5, $card->rating)); $s++): ?>
                                            <svg class="h-4 w-4 fill-amber-400 text-amber-400 sm:h-[18px] sm:w-[18px]" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-1.5 text-[0.92rem] leading-6 text-slate-600">
                                <?php if ($card->cuisine !== ''): ?>
                                    <p><span class="font-bold text-slate-800">Cuisine:</span> <?= htmlspecialchars($card->cuisine) ?></p>
                                <?php endif; ?>
                                <?php if ($card->address !== ''): ?>
                                    <p><span class="font-bold text-slate-800">Address:</span> <?= htmlspecialchars($card->address) ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if ($card->description !== ''): ?>
                                <p class="text-[0.96rem] leading-7 text-slate-600">
                                    <?= htmlspecialchars($card->description) ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($card->slug !== null): ?>
                                <div class="mt-auto flex items-center justify-center gap-2.5 pt-3">
                                    <a href="/restaurant/<?= $card->slug ?>"
                                       class="inline-flex min-w-[106px] justify-center rounded-[14px] bg-red px-4 py-2.5 text-base font-medium text-white transition-colors duration-200 hover:bg-royal-blue focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                                        <?= htmlspecialchars($labelAbout) ?>
                                    </a>
                                    <a href="/restaurant/<?= $card->slug ?>/reservation"
                                       class="inline-flex min-w-[106px] justify-center rounded-[14px] bg-red px-4 py-2.5 text-base font-medium text-white transition-colors duration-200 hover:bg-royal-blue focus:outline-none focus-visible:ring-2 focus-visible:ring-red focus-visible:ring-offset-2">
                                        <?= htmlspecialchars($labelBook) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<script src="/assets/js/restaurant-filters.js"></script>
