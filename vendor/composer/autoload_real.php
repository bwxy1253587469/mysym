<?php

class ComposerAutoloaderInit
{
    private static $lists;
    public static function getLoader()
    {
        self::setPsr4();
        self::register();
    }
    public static function setPsr4()
    {
        $map = require __DIR__.'/autoload_psr4.php';
        self::$lists = $map;
    }
    public static function register()
    {
        spl_autoload_register(array('ComposerAutoloaderInit', 'loadClassLoader'), true, true);
    }
    public static function loadClassLoader($class)
    {
        $namespace = '';
        while (($namespace .= strstr($class, '\\', true)) && ($class = strstr($class, '\\'))) {
            $namespace .= '\\';
            $class = trim($class, '\\');
            if (isset(self::$lists[$namespace])) {
                include_once(self::$lists[$namespace][0].'\\'.$class.'.php');
                break;
            }
        }
    }
}
