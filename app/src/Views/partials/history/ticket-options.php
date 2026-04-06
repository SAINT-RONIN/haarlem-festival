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
use App\View\ViewRenderer;

/** @var TicketOptions $ticketOptions */
$ticketOptions = $viewModel->ticketOptionsData;
$heading = $ticketOptions->headingText;
$cards   = $ticketOptions->pricingCards;
?>
<section class="self-stretch px-6 lg:px-24 py-12 inline-flex flex-col justify-center items-center gap-12 overflow-hidden">
    <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
        <?= htmlspecialchars($heading) ?>
    </div>
    <div class="self-stretch grid grid-cols-1 md:grid-cols-2 gap-12 xl:gap-48 justify-items-center items-stretch">
        <?php foreach ($cards as $card): ?>
            <?php /** @var PricingCard $card */ ?>
            <?php ViewRenderer::render(__DIR__ . '/ticket-type.php', ['card' => $card]); ?>
        <?php endforeach; ?>
    </div>
</section>
