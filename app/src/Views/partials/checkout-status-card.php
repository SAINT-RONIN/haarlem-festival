<?php
/**
 * Shared card layout for checkout status pages.
 *
 * @var string $title
 * @var string $message
 * @var array<string, string> $details
 * @var string $primaryHref
 * @var string $primaryLabel
 * @var string $secondaryHref
 * @var string $secondaryLabel
 */
$title = (string) ($title ?? 'Checkout status');
$message = (string) ($message ?? '');
$details ??= [];
/*
 * Rendered inside the shell's <main>; caller sets mainClass on its
 * PublicPageLayout to 'w-full bg-[#F5F1EB] min-h-screen px-4 sm:px-8 lg:px-24 py-12'.
 */
?>
    <section class="max-w-3xl mx-auto p-6 sm:p-8 bg-white rounded-3xl outline outline-2 outline-offset-[-2px] outline-gray-200">
        <h1 class="text-gray-900 text-3xl sm:text-4xl font-bold font-['Montserrat'] leading-tight">
            <?= htmlspecialchars($title) ?>
        </h1>

        <?php if ($message !== ''): ?>
            <p class="mt-4 text-gray-700 text-base sm:text-lg font-normal font-['Montserrat'] leading-7">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <?php require __DIR__ . '/checkout-status-metadata.php'; ?>
        <?php require __DIR__ . '/checkout-status-actions.php'; ?>
    </section>

