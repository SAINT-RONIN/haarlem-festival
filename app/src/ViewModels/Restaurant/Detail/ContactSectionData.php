<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the Contact card on the restaurant detail page.
 *
 * Groups address, phone, email, website, open hours, and their CMS labels.
 */
final readonly class ContactSectionData
{
    /**
     * @param string[] $timeSlots  Available session times (e.g. ["17:00", "19:15", "21:30"])
     */
    public function __construct(
        public string $address,
        public string $phone,
        public string $email,
        public string $website,
        public array  $timeSlots,

        // CMS labels
        public string $labelTitle     = 'Contact',
        public string $labelAddress   = 'ADDRESS',
        public string $labelContact   = 'CONTACT',
        public string $labelOpenHours = 'OPEN HOURS FOR YUMMY',
    ) {}
}
