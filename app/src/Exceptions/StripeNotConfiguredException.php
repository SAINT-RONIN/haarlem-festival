<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when Stripe API keys or webhook secrets are not configured.
 */
class StripeNotConfiguredException extends AppException {}
