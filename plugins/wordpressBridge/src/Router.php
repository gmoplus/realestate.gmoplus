<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: REQUESTS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

namespace Flynax\Plugin\WordPressBridge;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * Class Router
 *
 * @since 2.0.0
 *
 * @package Flynax\Plugin\WordPressBridge
 */
class Router
{
    /**
     * @var array - Registered routes
     */
    protected $routes = array();

    /**
     * @var string - Request URI
     */
    protected $uri;

    /**
     * @var string - Request method
     */
    protected $method;

    /**
     * @var  object - Fast route lib dispatcher
     */
    protected $dispatcher;

    /**
     * Load and define file with routes
     *
     * @param string $file - Routes file full path
     *
     * @return static - Current class instance
     */
    public static function load($file)
    {
        $router = new static();
        $router->define(Request::uri(), Request::method());
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $route) use ($file) {
            require $file;
        });


        $routeInfo = $dispatcher->dispatch($router->method, $router->uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                Response::error('Route was not found', 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                Response::error('Method is not allowed', 405);
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $handlerInfo = explode('@', $handler);
                $controllerName = $handlerInfo[0];
                $method = $handlerInfo[1];
                $controller = "\\Flynax\\Plugin\\WordPressBridge\\Controllers\\{$controllerName}";

                if (!method_exists($controller, $method)) {
                    Response::error(
                        sprintf(
                            "Method '%s()' was not found in controller '%s'",
                            $method,
                            $controllerName
                        ),
                        404
                    );
                    return;
                }

                if (method_exists($controller, $method)) {
                    $instance = new $controller();
                    $instance->{$method}();
                }

                break;
        }

        return $router;
    }

    /**
     * Set request URI and method
     *
     * @param string $uri    - Request URI
     * @param string $method - Request method
     *
     * @return self $this  - Instance of the current class
     */
    public function define($uri, $method)
    {
        $this->uri = $uri;
        $this->method = $method;

        return $this;
    }
}
