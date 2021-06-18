<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Pollen\Support\Exception\ManagerRuntimeException;
use RuntimeException;

class Kernel implements KernelInterface
{

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * @var ApplicationInterface
     */
    protected $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;

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
    public function getApp(): ApplicationInterface
    {
        if (!$this->app instanceof ApplicationInterface) {
            throw new RuntimeException('Unable to retrieve Application instance');
        }
        return $this->app;
    }
}
