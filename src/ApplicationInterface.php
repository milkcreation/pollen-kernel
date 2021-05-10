<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Pollen\Container\ContainerInterface;

interface ApplicationInterface extends ContainerInterface
{
    /**
     * Chargement.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération du numéro de version de l'application.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Déclaration des aliases de services fournis par le conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function registerAliases(): void;

    /**
     * Détermine si l'application est lancée dans une console.
     *
     * @return boolean
     */
    public function runningInConsole(): bool;
}