<?php

use Illuminate\Database\Query\Builder as QueryBuilder;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Config\ConfiguratorInterface;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Field\FieldDriverInterface;
use Pollen\Field\FieldManagerInterface;
use Pollen\Filesystem\FilesystemInterface;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Kernel\ApplicationInterface;
use Pollen\Kernel\Kernel;
use Pollen\Partial\PartialDriverInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Http\RequestInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Form\FormInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Support\Env;
use Pollen\Validation\ValidatorInterface;

if (!function_exists('app')) {
    /**
     * Instance de l'application
     *
     * @param string|null $abstract Nom de qualification du service.
     *
     * @return ApplicationInterface|mixed
     */
    function app(?string $abstract = null)
    {
        $app = Kernel::getInstance()->getApp();

        if ($abstract === null) {
            return $app;
        }

        return $app[$abstract] ?? null;
    }
}

if (!function_exists('asset')) {
    /**
     * Instance du gestionnaire des assets.
     *
     * @return AssetManagerInterface
     */
    function asset(): AssetManagerInterface
    {
        return app(AssetManagerInterface::class);
    }
}

if (!function_exists('config')) {
    /**
     * Config - Gestionnaire de configuration de l'application.
     * {@internal
     * - null $key Retourne l'instance du controleur de configuration.
     * - array $key Définition d'attributs de configuration.
     * - string $key Récupération de la valeur d'un attribut de configuration.
     * }
     *
     * @param null|array|string $key
     * @param mixed $default
     *
     * @return ConfiguratorInterface|mixed
     */
    function config($key = null, $default = null)
    {
        /* @var ConfiguratorInterface $config */
        $config = app(ConfiguratorInterface::class);

        if (is_null($key)) {
            return $config;
        }
        if (is_array($key)) {
            $config->set($key);

            return $config;
        }
        return $config->get($key, $default);
    }
}

if (!function_exists('database')) {
    /**
     * Instance du gestionnaire de base de données|Constructeur de requêtes d'une table de la base de données.
     *
     * @param string|null $table
     *
     * @return DatabaseManagerInterface|QueryBuilder|null
     */
    function database(?string $table = null)
    {
        /* @var DatabaseManagerInterface $manager */
        $manager = app(DatabaseManagerInterface::class);

        if ($table === null) {
            return $manager;
        }
        return $manager::table($table);
    }
}

if (!function_exists('env')) {
    /**
     * Récupération d'une variables d'environnement.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('event')) {
    /**
     * Instance du répartiteur d'événements.
     *
     * @return EventDispatcherInterface
     */
    function event(): EventDispatcherInterface
    {
        return app(EventDispatcherInterface::class);
    }
}

if (!function_exists('field')) {
    /**
     * Instance du gestionnaire de champs|Instance d'un champ déclaré.
     *
     * @param string|null $alias
     * @param mixed $idOrParams
     * @param array $params
     *
     * @return FieldManagerInterface|FieldDriverInterface|null
     */
    function field(?string $alias = null, $idOrParams = null, array $params = [])
    {
        /* @var FieldManagerInterface $manager */
        $manager = app(FieldManagerInterface::class);

        if (is_null($alias)) {
            return $manager;
        }
        return $manager->get($alias, $idOrParams, $params);
    }
}

if (!function_exists('form')) {
    /**
     * Instance du gestionnaire de formulaires|Instance d'un formulaire.
     *
     * @param string|null $name
     *
     * @return FormManagerInterface|FormInterface|null
     */
    function form(?string $name = null)
    {
        /* @var FormManagerInterface $manager */
        $manager = app(FormManagerInterface::class);

        if ($name === null) {
            return $manager;
        }
        return $manager->get($name);
    }
}

if (!function_exists('logger')) {
    /**
     * Instance du gestionnaire de journalisation|Déclaration d'un message de journalisation.
     *
     * @param string|null $message
     * @param array $context
     *
     * @return LogManagerInterface|void
     */
    function logger(?string $message = null, array $context = []): ?LogManagerInterface
    {
        /* @var LogManagerInterface $manager */
        $manager = app(LogManagerInterface::class);

        if ($message === null) {
            return $manager;
        }
        $manager->debug($message, $context);
    }
}

if (!function_exists('partial')) {
    /**
     * Instance du gestionnaire de portions d'affichage|Instance d'une portion d'affichage déclarée.
     *
     * @param string|null $alias
     * @param mixed $idOrParams
     * @param array $params
     *
     * @return PartialManagerInterface|PartialDriverInterface|null
     */
    function partial(?string $alias = null, $idOrParams = null, array $params = [])
    {
        /* @var PartialManagerInterface $manager */
        $manager = app(PartialManagerInterface::class);

        if (is_null($alias)) {
            return $manager;
        }
        return $manager->get($alias, $idOrParams, $params);
    }
}

if (!function_exists('request')) {
    /**
     * Instance de la requête HTTP principale.
     *
     * @return RequestInterface
     */
    function request(): RequestInterface
    {
        return app(RequestInterface::class);
    }
}

if (!function_exists('route')) {
    /**
     * Récupération de l'url d'une route déclarée.
     *
     * @param string $name
     * @param array $parameters
     * @param boolean $absolute
     *
     * @return string|null
     */
    function route(string $name, array $parameters = [], bool $absolute = true): ?string
    {
        /* @var RouterInterface $router */
        $router = app(RouterInterface::class);

        return $router->getNamedRouteUrl($name, $parameters, $absolute);
    }
}

if (!function_exists('storage')) {
    /**
     * Gestionnaire de système de fichier|Instance d'un point de montage.
     *
     * @param string|null $name
     *
     * @return StorageManagerInterface|FilesystemInterface
     */
    function storage(?string $name = null)
    {
        /* @var StorageManagerInterface $manager */
        $manager = app(StorageManagerInterface::class);

        if ($name === null) {
            return $manager;
        }
        return $manager->disk($name);
    }
}

if (!function_exists('validator')) {
    /**
     * Instance du gestionnaire de validation.
     *
     * @return ValidatorInterface
     */
    function validator(): ValidatorInterface
    {
        return app(ValidatorInterface::class);
    }
}