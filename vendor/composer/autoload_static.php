<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7b318f48fe05b0b2ad42cbe9dc5a7b74
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Mustache' => 
            array (
                0 => __DIR__ . '/..' . '/mustache/mustache/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7b318f48fe05b0b2ad42cbe9dc5a7b74::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7b318f48fe05b0b2ad42cbe9dc5a7b74::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7b318f48fe05b0b2ad42cbe9dc5a7b74::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
