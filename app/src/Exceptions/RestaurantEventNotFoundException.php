<?php

declare(strict_types=1);

namespace App\Exceptions;

final class RestaurantEventNotFoundException extends NotFoundException
{
    public function __construct(string $slug)
    {
        parent::__construct('Restaurant event slug', $slug);
    }
}
