<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when a requested entity is not found.
 *
 * Thrown by Services when a required entity does not exist.
 * Controllers should catch this and render a 404 page.
 */
class NotFoundException extends \Exception
{
    private string $entity;
    private int|string $entityId;

    public function __construct(string $entity, int|string $id)
    {
        $this->entity = $entity;
        $this->entityId = $id;
        parent::__construct("{$entity} with ID {$id} not found");
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getEntityId(): int|string
    {
        return $this->entityId;
    }
}

