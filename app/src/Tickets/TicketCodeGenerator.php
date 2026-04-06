<?php

declare(strict_types=1);

namespace App\Tickets;

use App\Tickets\Interfaces\ITicketCodeGenerator;

/**
 * Generates compact uppercase ticket codes that fit inside a fixed QR version.
 */
final class TicketCodeGenerator implements ITicketCodeGenerator
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function generate(): string
    {
        return 'HF-' . $this->encodeBase32(random_bytes(10));
    }

    private function encodeBase32(string $bytes): string
    {
        $alphabet = self::ALPHABET;
        $buffer = 0;
        $bitCount = 0;
        $output = '';

        foreach (unpack('C*', $bytes) ?: [] as $byte) {
            $buffer = ($buffer << 8) | $byte;
            $bitCount += 8;

            while ($bitCount >= 5) {
                $bitCount -= 5;
                $output .= $alphabet[($buffer >> $bitCount) & 0x1F];
            }
        }

        if ($bitCount > 0) {
            $output .= $alphabet[($buffer << (5 - $bitCount)) & 0x1F];
        }

        return $output;
    }
}
