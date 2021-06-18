<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Exception;
use Nette\Schema\Expect;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Config\Configurator;
use Pollen\Config\ConfiguratorInterface;
use Pollen\Container\BootableServiceProviderInterface;
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
use Pollen\Support\ProxyResolver;
use Pollen\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

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
     * @var static|null
     */
    private static $instance;

    /**
     * @var string
     */
    protected const  VERSION = '1.0.x-dev';

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $configParams = [];

    /**
     * @var string
     */
    protected $publicDir;

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * @var bool
     */
    protected $preBuilt = false;

    /**
     * @var int
     */
    protected $startTime;

    /**
     * @var ServiceProviderInterface[]
     */
    protected $serviceProviders = [];

    /**
     * @var BootableServiceProviderInterface[]|array
     */
    protected $bootableProviders = [];

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

        $this->preBuild();
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
     * Pré-initialisation.
     *
     * @return void
     */
    protected function preBuild(): void
    {
        if ($this->preBuilt === false) {
            $this->startTime = defined('START_TIME') ? START_TIME : microtime(true);

            $this->envLoad();

            $this->publicDir = Env::get('APP_PUBLIC_DIR', 'public');
            $this->publicPath = fs::normalizePath($this->basePath . fs::DS . $this->publicDir);

            $this->preBuildKernel();

            $this->preBuilt = true;
        }
    }

    /**
     * Pré-Initialisation du kernel.
     *
     * @return void
     */
    protected function preBuildKernel(): void
    {
        if (!$this->has(KernelInterface::class)) {
            $this->share(KernelInterface::class, new Kernel($this));
        }
    }

    /**
     * @inheritDoc
     */
    public function build(): ApplicationInterface
    {
        if (!$this->isBuilt()) {
            $this->buildConfig();
            $this->buildContainer();
            $this->buildProxies();
            $this->buildServices();
            $this->buildSession();

            $this->setBuilt();
        }

        return $this;
    }

    /**
     * Chargement de la configuration.
     *
     * @return void
     */
    protected function buildConfig(): void
    {
        $this->share(ConfiguratorInterface::class, $configurator = new Configurator());

        $configurator->addSchema('app_url', Expect::string());
        $configurator->addSchema('timezone', Expect::string());

        /** @todo Depuis le framework */
        $configurator->set(
            array_merge(
                [
                    'app_url'  => Env::get('APP_URL'),
                    'timezone' => Env::get('APP_TIMEZONE'),
                ],
                $this->configParams
            )
        );

        if ($tz = $configurator->get('timezone', ini_get('date.timezone'))) {
            date_default_timezone_set($tz);
        }

        mb_internal_encoding($configurator->get('charset', 'UTF-8'));
    }

    /**
     * Chargement du conteneur d'injection de dépendances.
     *
     * @return void
     */
    protected function buildContainer(): void
    {
        $this->enableAutoWiring(true);
        $this->share(ApplicationInterface::class, $this);

        $this->registerAliases();

        foreach ($this->getServiceProviders() as $definition) {
            if (is_string($definition)) {
                try {
                    $serviceProvider = new $definition();
                } catch (Exception $e) {
                    throw new RuntimeException(
                        'ServiceProvider [%s] instanciation return exception :%s',
                        $definition,
                        $e->getMessage()
                    );
                }
            } elseif (is_object($definition)) {
                $serviceProvider = $definition;
            } else {
                throw new RuntimeException(
                    'ServiceProvider [%s] type not supported',
                    $definition
                );
            }

            if (!$serviceProvider instanceof ServiceProviderInterface) {
                throw new RuntimeException(
                    'ServiceProvider [%s] must be an instance of %s',
                    $definition,
                    ServiceProviderInterface::class
                );
            }

            $serviceProvider->setContainer($this);
            if ($serviceProvider instanceof BootableServiceProviderInterface) {
                $this->bootableProviders[] = $serviceProvider;
            }
            $this->addServiceProvider($serviceProvider);
        }
    }

    /**
     * Initialisation des accesseurs.
     *
     * @return void
     */
    protected function buildProxies(): void
    {
        ProxyResolver::setContainer($this);

        if (class_exists(ProxyManager::class)) {
            $manager = new ProxyManager([], $this);
            foreach ($this->config->get('proxy', []) as $alias => $proxy) {
                $manager->addProxy($alias, $proxy);
            }

            $manager->enable(ProxyManager::ROOT_NAMESPACE_ANY);
        }
    }

    /**
     * Chargement des fournisseurs de services.
     *
     * @return void
     */
    protected function buildServices(): void
    {
        foreach ($this->bootableProviders as $bootableProvider) {
            $bootableProvider->boot();
        }
    }

    /**
     * Chargement de la session.
     *
     * @return void
     */
    protected function buildSession(): void
    {
        try {
            $this->session->start();

            $this->request->setSession($this->session->processor());
        } catch (Throwable $e) {
            unset($e);
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

    /**
     * @inheritDoc
     */
    public function setConfigParams(array $configParams): ApplicationInterface
    {
        $this->configParams = $configParams;

        return $this;
    }
}