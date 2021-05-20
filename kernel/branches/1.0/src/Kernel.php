<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use App\App;
use Exception;
use Pollen\Config\Config;
use Pollen\Config\ConfigInterface;
use Pollen\Container\BootableServiceProviderInterface;
use Pollen\Container\ServiceProviderInterface;
use Pollen\Proxy\ProxyManager;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\ProxyResolver;
use RuntimeException;

class Kernel implements KernelInterface
{
    use BootableTrait;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * @var ApplicationInterface
     */
    protected $app;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ServiceProviderInterface[]|array
     */
    protected $bootableProviders;

    /**
     * @var int
     */
    protected $startTime;

    /**
     * @param string|null $configDir
     */
    public function __construct(?string $configDir = null)
    {
        $this->config = ($configDir !== null) ? new Config($configDir) : new Config('');

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): KernelInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            $this->startTime = defined('START_TIME') ? START_TIME : microtime(true);

            $this->bootApp();
            $this->bootConfig();
            $this->bootContainer();
            $this->bootProxies();
            $this->bootServices();
            $this->bootSession();

            $this->app->boot();

            $this->setBooted();
        }
    }

    /**
     * Chargement de l'application.
     *
     * @return void
     */
    protected function bootApp(): void
    {
        $this->app = class_exists(App::class) ? new App() : new Application();

        if (!$this->app instanceof ApplicationInterface) {
            throw new RuntimeException(sprintf('Application must be an instance of %s', ApplicationInterface::class));
        }
    }

    /**
     * Chargement de la configuration.
     *
     * @return void
     */
    protected function bootConfig(): void
    {
        $this->config->setContainer($this->app);
        $this->app->share(ConfigInterface::class, $this->config);

        $tz = $this->config->get('timezone') ?: $this->app->request->server->get(
            'TZ',
            ini_get('date.timezone') ?: 'UTC'
        );
        date_default_timezone_set($tz);

        mb_internal_encoding($this->config->get('charset', 'UTF-8'));
    }

    /**
     * Chargement du conteneur d'injection de dépendances.
     *
     * @return void
     */
    protected function bootContainer(): void
    {
        $this->app->enableAutoWiring(true);
        $this->app->share(ApplicationInterface::class, $this->app);

        $this->app->registerAliases();

        $serviceProviders = $this->config->get('app.providers', []);

        foreach ($serviceProviders as $definition) {
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

            $serviceProvider->setContainer($this->app);
            if ($serviceProvider instanceof BootableServiceProviderInterface) {
                $this->bootableProviders[] = $serviceProvider;
            }
            $this->app->addServiceProvider($serviceProvider);
        }
    }

    /**
     * Chargement des accesseurs.
     *
     * @return void
     */
    protected function bootProxies(): void
    {
        ProxyResolver::setContainer($this->app);

        if (class_exists(ProxyManager::class)) {
            $manager = new ProxyManager([], $this->app);
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
    protected function bootServices(): void
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
    protected function bootSession(): void
    {
        try {
            $this->app->session->start();

            $this->app->request->setSession($this->app->session->processor());
        } catch (RuntimeException $e) {
            unset($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getApp(): ApplicationInterface
    {
        if (!$this->app instanceof ApplicationInterface) {
            throw new RuntimeException('Unable to retrieve Application instance');
        }
        return $this->app;
    }

    /**
     * @inheritDoc
     */
    public function getStartTime(): ?float
    {
        return $this->startTime;
    }
}
