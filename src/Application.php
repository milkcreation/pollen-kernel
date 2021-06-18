<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Config\ConfiguratorInterface;
use Pollen\Container\Container;
use Pollen\Container\ServiceProviderInterface;
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
use Pollen\Support\Concerns\BuildableTrait;
use Pollen\Support\Env;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Filesystem as fs;
use Pollen\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;
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
 * @property-read KernelInterface kernel
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
class Application extends Container implements ApplicationInterface
{
    use BuildableTrait;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $publicDir;

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * Instance partagée.
     * @var static|null
     */
    private static $instance;

    /**
     * Liste des fournisseurs de service déclarés
     * @var ServiceProviderInterface[]
     */
    protected $serviceProviders = [];

    /**
     * @var string
     */
    protected const  VERSION = '1.0.x-dev';

    /**
     * @return void
     */
    public function __construct(string $basePath)
    {
        $this->basePath = fs::normalizePath($basePath);

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }

        parent::__construct();

        $this->build();
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): ApplicationInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * Initialisation.
     *
     * @return void
     */
    protected function build(): void
    {
        if (!$this->isBuilt()) {
            $this->envLoad();

            $this->publicDir = Env::get('APP_PUBLIC', 'public');
            $this->publicPath = fs::normalizePath($this->basePath . fs::DS . $this->publicDir);

            $this->share(KernelInterface::class, new Kernel($this));

            $this->setBuilt();
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * Chargement des variables globales d'environnement.
     *
     * @return void
     */
    protected function envLoad(): void
    {
        try {
            $this->share(Dotenv::class, $dotenv = Dotenv::createImmutable($this->basePath));
            $dotenv->load();
        } catch (InvalidPathException $e) {
            unset($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function getServiceProviders(): array
    {
        return $this->serviceProviders;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * @inheritDoc
     */
    public function registerAliases(): void
    {
        foreach (
            [
                ApplicationInterface::class     => [
                    'app',
                    'container',
                    Container::class,
                    ContainerInterface::class,
                ],
                AssetManagerInterface::class    => [
                    'asset',
                ],
                ConfiguratorInterface::class    => [
                    'config',
                ],
                CookieJarInterface::class       => [
                    'cookie',
                ],
                DatabaseManagerInterface::class => [
                    'database',
                    'db',
                ],
                DebugManagerInterface::class    => [
                    'debug',
                ],
                EncrypterInterface::class       => [
                    'crypt',
                ],
                EventDispatcherInterface::class => [
                    'event',
                ],
                FieldManagerInterface::class    => [
                    'field',
                ],
                FormManagerInterface::class     => [
                    'form',
                ],
                KernelInterface::class          => [
                    'kernel',
                ],
                LogManagerInterface::class      => [
                    'log',
                ],
                MailManagerInterface::class     => [
                    'mail',
                ],
                MetaboxManagerInterface::class  => [
                    'metabox',
                ],
                PartialManagerInterface::class  => [
                    'partial',
                ],
                RequestInterface::class         => [
                    'request',
                ],
                RouterInterface::class          => [
                    'router',
                ],
                ServerRequestInterface::class   => [
                    'psr_request',
                ],
                SessionManagerInterface::class  => [
                    'session',
                ],
                StorageManagerInterface::class  => [
                    'storage',
                ],
                ValidatorInterface::class       => [
                    'validator',
                ],
            ] as $key => $aliases
        ) {
            foreach ($aliases as $alias) {
                $this->aliases[$alias] = $key;
            }
        }
    }

    /**
     * Détermine si l'application est lancée dans une console.
     *
     * @return boolean
     */
    public function runningInConsole(): bool
    {
        global $argv;

        if (isset($_ENV['APP_RUNNING_IN_CONSOLE'])) {
            return $_ENV['APP_RUNNING_IN_CONSOLE'] === 'true';
        }

        if (isset($argv[0]) && preg_match('/vendor\/bin\/bee$/', $argv[0])) {
            return true;
        }

        return isset($argv[0]) && ($argv[0] === 'console') && (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg');
    }
}