<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use App\App;
use Exception;
use Nette\Schema\Expect;
use Nette\Schema\Elements\Type;
use Pollen\Config\Configurator;
use Pollen\Config\ConfiguratorInterface;
use Pollen\Container\BootableServiceProviderInterface;
use Pollen\Container\ServiceProviderInterface;
use Pollen\Proxy\ProxyManager;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Env;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\ProxyResolver;
use RuntimeException;
use Throwable;

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
     * @var array
     */
    protected $config = [];

    /**
     * @var BootableServiceProviderInterface[]|array
     */
    protected $bootableProviders = [];

    /**
     * @var int
     */
    protected $startTime;

    public function __construct()
    {
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

        $this->app->share(KernelInterface::class, $this);
    }

    /**
     * Chargement de la configuration.
     *
     * @return void
     */
    protected function bootConfig(): void
    {
        $this->app->share(ConfiguratorInterface::class, $configurator = new Configurator());

        $configurator->addSchema('app_url', Expect::string());
        $configurator->addSchema('timezone', Expect::string());

        $configurator->set('truc', 'machin');
        /** @todo Depuis le framework */
        $configurator->set(
            array_merge(
                [
                    'app_url'  => Env::get('APP_URL'),
                    'timezone' => Env::get('APP_TIMEZONE'),
                ],
                $this->config
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
    protected function bootContainer(): void
    {
        $this->app->enableAutoWiring(true);
        $this->app->share(ApplicationInterface::class, $this->app);

        $this->app->registerAliases();

        foreach ($this->app->getServiceProviders() as $definition) {
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
            foreach ($this->app->config->get('proxy', []) as $alias => $proxy) {
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
        } catch (Throwable $e) {
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

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): KernelInterface
    {
        $this->config = $config;

        return $this;
    }
}
