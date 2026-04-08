<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Unexpected failure while dispatching a transactional email.
 */
final class EmailDeliveryException extends AppException {}
