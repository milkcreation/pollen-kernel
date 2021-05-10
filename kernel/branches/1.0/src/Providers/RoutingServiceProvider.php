<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use League\Uri\Http as HttpUri;
use Pollen\Http\Request;
use Pollen\Http\RequestInterface;
use Pollen\Routing\Router;
use Pollen\Routing\RouterInterface;
use Pollen\Routing\Middleware\XhrMiddleware;
use Pollen\Routing\Strategy\ApplicationStrategy;
use Pollen\Routing\Strategy\JsonStrategy;
use Laminas\Diactoros\ResponseFactory;
use Pollen\Container\BootableServiceProvider;
use tiFy\Routing\Url;

class RoutingServiceProvider extends BootableServiceProvider
{
    protected $provides = [
        RouterInterface::class,
        'routing.middleware.xhr',
        'routing.strategy.app',
        'routing.strategy.json',
        /** @todo */
        'url',
        'uri',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(RouterInterface::class, function () {
            return new Router([], $this->getContainer());
        });
        $this->registerMiddlewares();
        $this->registerStrategies();

        $this->getContainer()->share('url', function () {
            return new Url($this->getContainer()->get(RouterInterface::class));
        });

        $this->getContainer()->share('uri', function () {
            /** @var Request $request */
            $request = $this->getContainer()->get(RequestInterface::class);

            return HttpUri::createFromString($request->getUri());
        });

    }

    /**
     * Déclaration des middlewares.
     *
     * @return void
     */
    public function registerMiddlewares(): void
    {
        $this->getContainer()->add('routing.middleware.xhr', function () {
            return new XhrMiddleware();
        });
    }

    /**
     * Déclaration des stratégies.
     *
     * @return void
     */
    public function registerStrategies(): void
    {
        $this->getContainer()->add('routing.strategy.app', function () {
            $applicationStrategy = new ApplicationStrategy();
            $applicationStrategy->setContainer($this->getContainer());

            return $applicationStrategy;
        });
        $this->getContainer()->add('routing.strategy.json', function () {
            $jsonStrategy = new JsonStrategy(new ResponseFactory());
            $jsonStrategy->setContainer($this->getContainer());

            return $jsonStrategy;
        });
    }
}