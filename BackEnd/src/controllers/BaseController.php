<?php

namespace HostMyDocs\Controllers;

use Slim\Container;

abstract class BaseController
{

    /**
     * @var null|Container the current DI container of the Slim app object
     */
    protected $container = null;

    /**
     * @var null|string message to inform the client what was wrong in his request
     */
    protected $errorMessage = null;

    public abstract static function useRoute();

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}
