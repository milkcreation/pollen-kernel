<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Exception;
use Pollen\Container\Container;
use ReStatic\ProxyManager;
use RuntimeException;

class Application extends Container
{
    /**
     * Initialisation du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function bootContainer(): void
    {
        $this->enableAutoWiring(true);

        $this->share('config', $this->config());

        $this->serviceProviders = array_merge($this->config('service-providers', []), $this->serviceProviders);
        $bootableServiceProviders = [];

        foreach ($this->serviceProviders as $definition) {
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
            $bootableServiceProviders[] = $serviceProvider;
            $this->addServiceProvider($serviceProvider);
        }

        /** @var ServiceProviderInterface $serviceProvider */
        foreach ($bootableServiceProviders as $serviceProvider) {
            $serviceProvider->boot();
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

        if(isset($argv[0]) && preg_match('/vendor\/bin\/bee$/', $argv[0])) {
            return true;
        }

        return isset($argv[0]) && ($argv[0] === 'console') && (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg');
    }

    /**
     *
     */
    public function registerProxy()
    {
        $manager = new ProxyManager($this);
        foreach(config('app.proxy', []) as $alias => $proxy) {
            $manager->addProxy($alias, $proxy);
        }

        $manager->enable(ProxyManager::ROOT_NAMESPACE_ANY);
    }
}