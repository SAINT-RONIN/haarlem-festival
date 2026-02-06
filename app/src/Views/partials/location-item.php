<?php
/**
 * Location item partial - Single location row with name, address, and category badge.
 *
 * @var array $location Location data with keys: name, address, category, badgeClass
 */

$name = htmlspecialchars($location['name']);
$address = htmlspecialchars($location['address']);
$category = htmlspecialchars($location['category'] ?? 'venue');
$badgeClass = htmlspecialchars($location['badgeClass']);
?>

<button type="button" class="w-full flex justify-start items-start gap-1 sm:gap-[5px] group text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded-lg" aria-label="<?php echo $name; ?> - <?php echo $address; ?>">
    <span class="flex-1 p-2 sm:p-2.5 md:p-3 lg:p-3.5 bg-white group-hover:bg-gray-50 rounded-lg sm:rounded-[10px] outline outline-[0.50px] outline-offset-[-0.50px] outline-slate-800 flex flex-col justify-start items-start gap-0.5 sm:gap-1 overflow-hidden transition-colors duration-200">
        <strong class="self-stretch justify-start text-slate-800 text-xs sm:text-sm md:text-base font-bold leading-tight"><?php echo $name; ?></strong>
        <span class="self-stretch justify-start text-slate-800 text-[10px] sm:text-xs md:text-sm lg:text-base font-light leading-tight"><?php echo $address; ?></span>
    </span>
    <span class="w-6 sm:w-7 md:w-8 lg:w-9 self-stretch p-2 sm:p-2.5 md:p-3 lg:p-3.5 <?php echo $badgeClass; ?> rounded-lg sm:rounded-[10px] border sm:border-2 border-slate-800/70 transition-transform duration-200 group-hover:scale-105" aria-hidden="true"></span>
</button>

