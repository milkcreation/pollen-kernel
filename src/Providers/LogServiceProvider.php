<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Log\LogManager;
use Pollen\Log\LogManagerInterface;
use Pollen\Container\BootableServiceProvider;

class LogServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        LogManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(LogManagerInterface::class, function () {
            return new LogManager(config('log', []), $this->getContainer());
        });
    }
}