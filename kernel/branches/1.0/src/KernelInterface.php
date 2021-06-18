<?php

declare(strict_types=1);

namespace Pollen\Kernel;

interface KernelInterface
{
    /**
     * Récupération de l'instance de l'application.
     *
     * @return ApplicationInterface
     */
    public function getApp(): ApplicationInterface;
}
