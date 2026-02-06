<?php
/**
 * Schedule event card partial - Single event card in schedule.
 *
 * @var array $session Session data with keys: timeLabel, title, categoryLabel, borderClass
 */

$timeLabel = htmlspecialchars($session['timeLabel']);
$title = htmlspecialchars($session['title']);
$categoryLabel = htmlspecialchars($session['categoryLabel']);
$borderClass = htmlspecialchars($session['borderClass']);
?>

<div class="self-stretch p-3 sm:p-4 md:p-5 bg-white rounded-[12px] sm:rounded-[16px] md:rounded-[20px] inline-flex justify-start items-start gap-2 sm:gap-2.5 overflow-hidden">
    <div class="w-0.5 sm:w-1 self-stretch relative <?php echo $borderClass; ?> rounded-sm"></div>
    <div class="inline-flex flex-col justify-start items-start gap-1.5 sm:gap-2 md:gap-2.5">
        <div class="flex flex-col justify-start items-start">
            <div class="justify-start text-slate-800 text-xs sm:text-sm font-semibold leading-tight"><?php echo $timeLabel; ?></div>
            <div class="justify-start text-slate-800 text-sm sm:text-base font-normal leading-tight"><?php echo $title; ?></div>
        </div>
        <div class="justify-start text-slate-500 text-[10px] sm:text-xs font-medium leading-tight"><?php echo $categoryLabel; ?></div>
    </div>
</div>

