<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf52a20c4266c6507a5e09fca0fcc4d3e
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf52a20c4266c6507a5e09fca0fcc4d3e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf52a20c4266c6507a5e09fca0fcc4d3e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf52a20c4266c6507a5e09fca0fcc4d3e::$classMap;

        }, null, ClassLoader::class);
    }
}
