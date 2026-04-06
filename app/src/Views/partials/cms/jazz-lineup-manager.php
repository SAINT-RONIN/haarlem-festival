<?php
/**
 * Jazz lineup manager shown inside the Jazz page editor's artists section.
 *
 * @var \App\ViewModels\Cms\CmsJazzLineupManagerViewModel $jazzLineupManager
 */
?>

<div class="px-6 pb-6 pt-2 space-y-6 border-t border-dashed border-gray-200 bg-slate-50/70">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h4 class="text-sm font-semibold text-slate-900">Lineup Cards</h4>
            <p class="mt-1 text-sm text-slate-600">
                Add, edit, reorder, or remove the lineup cards here. This flow only manages the card fields, not a full artist detail page.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?= htmlspecialchars($jazzLineupManager->createCardUrl) ?>"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Create Lineup Card
            </a>
        </div>
    </div>

    <?php if ($jazzLineupManager->cards !== []): ?>
        <div class="grid gap-5 xl:grid-cols-2">
            <?php foreach ($jazzLineupManager->cards as $card): ?>
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <?php if ($card->imageUrl !== ''): ?>
                        <img src="<?= htmlspecialchars($card->imageUrl) ?>"
                             alt="<?= htmlspecialchars($card->name) ?>"
                             class="h-52 w-full object-cover">
                    <?php else: ?>
                        <div class="flex h-52 w-full items-center justify-center bg-slate-200 text-sm font-medium text-slate-500">
                            No card image
                        </div>
                    <?php endif; ?>

                    <div class="space-y-4 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h5 class="text-xl font-semibold text-royal-blue"><?= htmlspecialchars($card->name) ?></h5>
                                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500"><?= htmlspecialchars($card->style) ?></p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Sort <?= $card->sortOrder > 0 ? $card->sortOrder : 'Auto' ?>
                            </span>
                        </div>

                        <p class="text-sm leading-6 text-slate-700"><?= htmlspecialchars($card->description) ?></p>
                        <p class="text-sm font-medium text-slate-600"><?= htmlspecialchars($card->performanceSummary) ?></p>

                        <div class="flex flex-wrap gap-3">
                            <a href="<?= htmlspecialchars($card->editUrl) ?>"
                               class="inline-flex items-center gap-2 rounded-lg bg-royal-blue px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red">
                                <i data-lucide="pencil" class="h-4 w-4"></i>
                                Edit Card
                            </a>

                            <?php if ($card->profileUrl !== null && $card->profileUrl !== ''): ?>
                                <a href="<?= htmlspecialchars($card->profileUrl) ?>"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">
                                    <i data-lucide="external-link" class="h-4 w-4"></i>
                                    View Public Page
                                </a>
                            <?php endif; ?>

                            <button type="button"
                                    data-post-action="<?= htmlspecialchars($card->removeAction) ?>"
                                    data-post-csrf="<?= htmlspecialchars($jazzLineupManager->removeCsrfToken) ?>"
                                    data-post-return-to="<?= htmlspecialchars($jazzLineupManager->returnTo) ?>"
                                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-100">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                Remove From Section
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <h5 class="text-lg font-semibold text-slate-900">No lineup cards are live yet</h5>
            <p class="mx-auto mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                Create a lineup card or add an existing artist below to populate the discover lineup slider on the Jazz page.
            </p>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h5 class="text-sm font-semibold text-slate-900">Available Artist Profiles</h5>
            <p class="mt-1 text-sm text-slate-600">Use these existing artists as lineup cards without opening the full artist-detail editor.</p>
        </div>

        <?php if ($jazzLineupManager->availableArtists !== []): ?>
            <div class="divide-y divide-slate-200">
                <?php foreach ($jazzLineupManager->availableArtists as $artist): ?>
                    <div class="flex flex-col gap-4 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-4">
                            <?php if ($artist->imageUrl !== ''): ?>
                                <img src="<?= htmlspecialchars($artist->imageUrl) ?>"
                                     alt="<?= htmlspecialchars($artist->name) ?>"
                                     class="h-16 w-16 rounded-xl object-cover">
                            <?php else: ?>
                                <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-100 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                                    No Img
                                </div>
                            <?php endif; ?>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h6 class="text-base font-semibold text-slate-900"><?= htmlspecialchars($artist->name) ?></h6>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                        <?= htmlspecialchars($artist->style) ?>
                                    </span>
                                    <?php if ($artist->sortOrder > 0): ?>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                            Sort <?= $artist->sortOrder ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600"><?= htmlspecialchars($artist->description) ?></p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="button"
                                    data-post-action="<?= htmlspecialchars($artist->addAction) ?>"
                                    data-post-csrf="<?= htmlspecialchars($jazzLineupManager->addCsrfToken) ?>"
                                    data-post-return-to="<?= htmlspecialchars($jazzLineupManager->returnTo) ?>"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                                <i data-lucide="plus" class="h-4 w-4"></i>
                                Add To Section
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="px-5 py-6 text-sm text-slate-600">
                Every active artist profile is already in the lineup, or there are no other active artist profiles available.
            </div>
        <?php endif; ?>
    </div>
</div>
