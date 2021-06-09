<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Config\ConfiguratorInterface;
use Pollen\Container\ContainerInterface;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Encryption\EncrypterInterface;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Field\FieldManagerInterface;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Http\RequestInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Mail\MailManagerInterface;
use Pollen\Metabox\MetaboxManagerInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Session\SessionManagerInterface;
use Pollen\Validation\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property-read AssetManagerInterface asset
 * @property-read ConfiguratorInterface config
 * @property-read CookieJarInterface cookie
 * @property-read EncrypterInterface crypt
 * @property-read DatabaseManagerInterface database
 * @property-read DatabaseManagerInterface db
 * @property-read DebugManagerInterface debug
 * @property-read EventDispatcherInterface event
 * @property-read FieldManagerInterface field
 * @property-read FormManagerInterface form
 * @property-read LogManagerInterface log
 * @property-read MailManagerInterface mail
 * @property-read MetaboxManagerInterface metabox
 * @property-read PartialManagerInterface partial
 * @property-read RequestInterface request
 * @property-read RouterInterface router
 * @property-read ServerRequestInterface psr_request
 * @property-read SessionManagerInterface session
 * @property-read StorageManagerInterface storage
 * @property-read ValidatorInterface validator
 */
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