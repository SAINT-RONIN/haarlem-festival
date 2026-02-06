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

<div class="self-stretch p-5 bg-white rounded-[20px] inline-flex justify-start items-start gap-2.5 overflow-hidden">
    <div class="w-1 self-stretch relative <?php echo $borderClass; ?> rounded-sm"></div>
    <div class="inline-flex flex-col justify-start items-start gap-2.5">
        <div class="flex flex-col justify-start items-start">
            <div class="justify-start text-slate-800 text-sm font-semibold leading-5"><?php echo $timeLabel; ?></div>
            <div class="justify-start text-slate-800 text-base font-normal leading-5"><?php echo $title; ?></div>
        </div>
        <div class="justify-start text-slate-500 text-xs font-medium leading-4"><?php echo $categoryLabel; ?></div>
    </div>
</div>

