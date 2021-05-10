<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Database\DatabaseManager;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Container\BootableServiceProvider;

class DatabaseServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        DatabaseManagerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(DatabaseManagerInterface::class, function () {
           return new DatabaseManager();
        });
    }
}