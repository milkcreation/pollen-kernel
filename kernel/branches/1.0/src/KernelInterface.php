<?php

declare(strict_types=1);

namespace Pollen\Kernel;

use Pollen\Http\RequestInterface;
use Pollen\Http\ResponseInterface;

interface KernelInterface
{
    /**
     * Récupération de l'instance de l'application.
     *
     * @return ApplicationInterface
     */
    public function getApp(): ApplicationInterface;

    /**
     * Traitement de la requête HTTP.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface;

    /**
     * Envoi de la réponse HTTP.
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    public function send(ResponseInterface $response): bool;

    /**
     * Termine le cycle de la requête et de la réponse HTTP.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function terminate(RequestInterface $request, ResponseInterface $response): void;
}
