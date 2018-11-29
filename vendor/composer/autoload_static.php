<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitca17fc72ef743cf7658da899fdde858c
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Predis\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitca17fc72ef743cf7658da899fdde858c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitca17fc72ef743cf7658da899fdde858c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
