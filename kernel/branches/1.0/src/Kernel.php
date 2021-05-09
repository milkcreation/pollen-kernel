<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use App\App;
use Pollen\Config\Config;
use Pollen\Config\ConfigInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\ProxyResolver;

class Kernel implements KernelInterface
{
    use BootableTrait;

    /**
     * @var ConfigInterface
     */
    protected $config;

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
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            /** @deprecaded */
            if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) {
                return;
            }

            $this->startTime = defined('START_TIME') ? START_TIME : microtime(true);

            $app = class_exists(App::class) ? new App() : new Application();

            ProxyResolver::setContainer($app);

            $app->bootContainer();


            $this->setBooted();
        }
    }

    /**
     * Récupération de l'heure de démarrage.
     *
     * @return float
     */
    public function getStartTime(): ?float
    {
        return $this->startTime;
    }
}
