<?php
/**
 * Ticket options section for the History page.
 *
 * Expects a \App\ViewModels\History\HistoryPageViewModel as $viewModel
 * and uses its ticketOptionsData property.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

use App\ViewModels\History\TicketOptions;
use App\ViewModels\History\PricingCard;

/** @var TicketOptions $ticketOptions */
$ticketOptions = $viewModel->ticketOptionsData;
$heading = $ticketOptions->headingText;
$cards   = $ticketOptions->pricingCards;
?>
<section class="self-stretch px-6 lg:px-24 py-12 inline-flex flex-col justify-center items-center gap-12 overflow-hidden">
    <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
        <?= htmlspecialchars($heading) ?>
    </div>
    <div class="self-stretch inline-flex flex-wrap justify-center items-stretch gap-12 xl:gap-48">
        <?php foreach ($cards as $card): ?>
            <?php /** @var PricingCard $card */ ?>
            <?php require __DIR__ . '/ticket-type.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
