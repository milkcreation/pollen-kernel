<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Event\EventDispatcher;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Container\BootableServiceProvider;

class EventServiceProvider extends BootableServiceProvider
{
    protected $provides = [
        EventDispatcherInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(EventDispatcherInterface::class, function () {
            return new EventDispatcher([], $this->getContainer());
        });
    }
}