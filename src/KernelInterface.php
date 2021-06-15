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

    /**
     * Récupération de l'instance de l'application.
     *
     * @return ApplicationInterface
     */
    public function getApp(): ApplicationInterface;

    /**
     * Récupération de l'heure de démarrage.
     *
     * @return float|null
     */
    public function getStartTime(): ?float;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $config
     *
     * @return static
     */
    public function setConfig(array $config): KernelInterface;
}