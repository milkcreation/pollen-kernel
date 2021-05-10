<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Pollen\Config\ConfigInterface;
use Pollen\Container\Container;
use Pollen\Http\RequestInterface;
use Pollen\Support\Concerns\BootableTrait;
use Psr\Container\ContainerInterface;

/**
 * @property-read ConfigInterface config
 * @property-read RequestInterface request
 */
class Application extends Container implements ApplicationInterface
{
    use BootableTrait;

    /**
     * @var string[]
     */
    protected $aliases = [];

    /**
     * @var string
     */
    protected const  VERSION = '1.0.x-dev';

    /**
     * @inheritDoc
     */
    public function boot(): void { }

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
        foreach([
            ApplicationInterface::class => [
                'app',
                Container::class,
                ContainerInterface::class
            ],
            ConfigInterface::class => [
                'config'
            ],
            RequestInterface::class => [
                'request'
            ]
        ] as $key => $aliases) {
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