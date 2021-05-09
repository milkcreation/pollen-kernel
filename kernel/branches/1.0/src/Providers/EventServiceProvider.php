<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Event\EventDispatcher;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Container\BaseServiceProvider;

class EventServiceProvider extends BaseServiceProvider
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