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

namespace Flynax\Plugin\WordPressBridge\Traits;

/**
 * Trait SingletonTrait
 *
 * @since 2.0.0
 *
 * @package Flynax\Plugin\WordPressBridge\Traits
 */
trait SingletonTrait
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * Getting current instance of the class
     * @return self
     */
    final public static function getInstance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    /**
     * Get current instance of the class (helper of the getInstance() method)
     *
     * @return static
     */
    final public static function i()
    {
        return self::getInstance();
    }

    /**
     * SingletonTrait constructor.
     */
    final public function __construct()
    {
    }

    /**
     * Prevent class serializing
     */
    final public function __wakeup()
    {
    }

    /**
     * Prevent cloning object
     */
    final public function __clone()
    {
    }
}
