<?php declare(strict_types=1);

namespace App\Generator;

use Ramsey\Uuid\Uuid;

/**
 * The "Uuid4Generator" class
 */
class Uuid4Generator implements RandomStringGenerator
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
