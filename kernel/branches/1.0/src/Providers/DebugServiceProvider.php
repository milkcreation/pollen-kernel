<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Debug\DebugBarInterface;
use Pollen\Debug\DebugManager;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Debug\ErrorHandlerInterface;
use Pollen\Debug\PhpDebugBarDriver;
use Pollen\Debug\WhoopsErrorHandler;
use Pollen\Container\BootableServiceProvider;

class DebugServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        DebugManagerInterface::class,
        DebugBarInterface::class,
        ErrorHandlerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            DebugManagerInterface::class,
            function () {
                return new DebugManager(config('debug', []), $this->getContainer());
            }
        );

        $this->getContainer()->add(
            DebugBarInterface::class,
            function () {
                return new PhpDebugBarDriver($this->getContainer()->get(DebugManagerInterface::class));
            }
        );

        $this->getContainer()->share(
            ErrorHandlerInterface::class,
            function () {
                return new WhoopsErrorHandler($this->getContainer()->get(DebugManagerInterface::class));
            }
        );
    }
}