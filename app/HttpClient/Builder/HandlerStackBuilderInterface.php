<?php declare(strict_types=1);

namespace App\HttpClient\Builder;

use GuzzleHttp\HandlerStack;

/**
 * The "HandlerStackBuilderInterface" interface
 */
interface HandlerStackBuilderInterface
{

    /**
     * Build handler stack
     *
     * @return HandlerStack
     */
    public function build(): HandlerStack;
}
