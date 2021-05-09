<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Pollen\Support\Concerns\BootableTraitInterface;

interface KernelInterface extends BootableTraitInterface
{
    /**
     * Chargement
     *
     * @return void
     */
    public function boot(): void;
    
}
