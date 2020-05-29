<?php declare(strict_types=1);

namespace App\Generator;

/**
 * The "RandomStringGenerator" interface
 */
interface RandomStringGenerator
{
    public function generate(): string;
}
