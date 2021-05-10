<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Form\FormManager;
use Pollen\Form\FormManagerInterface;
use Pollen\Container\BootableServiceProvider;

class FormServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        FormManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(FormManagerInterface::class, function () {
            return new FormManager([], $this->getContainer());
        });
    }
}