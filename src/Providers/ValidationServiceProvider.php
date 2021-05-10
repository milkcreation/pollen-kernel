<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Validation\Validator;
use Pollen\Validation\ValidatorInterface;
use Pollen\Container\BootableServiceProvider;

class ValidationServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        ValidatorInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(ValidatorInterface::class, function () {
            return new Validator();
        });
    }
}