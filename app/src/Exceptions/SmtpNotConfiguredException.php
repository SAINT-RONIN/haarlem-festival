<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when SMTP is not configured or the recipient address is invalid for sending.
 */
class SmtpNotConfiguredException extends AppException {}
