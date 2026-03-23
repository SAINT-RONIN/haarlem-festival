<?php

declare(strict_types=1);

namespace App\Exceptions;

final class StorytellingEventNotFoundException extends NotFoundException
{
    public function __construct(string $slug)
    {
        parent::__construct('Storytelling event slug', $slug);
    }
}
