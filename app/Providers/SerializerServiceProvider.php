<?php

namespace App\Providers;

use App\Normalizer\PandaScoreUpcomingMatchDtoNormalizer;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        ObjectNormalizer::class => ObjectNormalizer::class,
        PandaScoreUpcomingMatchDtoNormalizer::class => PandaScoreUpcomingMatchDtoNormalizer::class
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->app->singleton(JsonEncoder::class, function (Application $app) {
            return new JsonEncoder(null, null);
        });

        $this->app->singleton(Serializer::class, function (Application $app) {
            $normalizers = [
                $app->get(PandaScoreUpcomingMatchDtoNormalizer::class),
                $app->get(ObjectNormalizer::class),
            ];
            $encoders = [
                $app->get(JsonEncoder::class),
            ];

            return new Serializer($normalizers, $encoders);
        });

        $this->app->bind(SerializerInterface::class, Serializer::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
