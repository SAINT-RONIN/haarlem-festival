<?php
/**
 * Location item partial - Single location row with name, address, and category badge.
 *
 * @var array $location Location data with keys: name, address, category, badgeClass
 */

$name = htmlspecialchars($location['name']);
$address = htmlspecialchars($location['address']);
$badgeClass = htmlspecialchars($location['badgeClass']);
?>

<div class="w-full inline-flex justify-start items-start gap-[5px] cursor-pointer group">
    <div class="flex-1 p-3.5 bg-white group-hover:bg-gray-50 rounded-[10px] outline outline-[0.50px] outline-offset-[-0.50px] outline-slate-800 inline-flex flex-col justify-start items-start gap-[5px] overflow-hidden transition-colors duration-200">
        <div class="self-stretch justify-start text-slate-800 text-base font-bold leading-4"><?php echo $name; ?></div>
        <div class="self-stretch justify-start text-slate-800 text-base font-light"><?php echo $address; ?></div>
    </div>
    <div class="w-9 self-stretch p-3.5 <?php echo $badgeClass; ?> rounded-[10px] border-2 border-slate-800/70 transition-transform duration-200 group-hover:scale-105"></div>
</div>

