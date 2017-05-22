<?php

/**
 * Helper for loading PDFsquid classes
 * Use ONLY if composer is not available on your system.
 * Just require this file in your code.
 * @param $dir
 * @throws Exception
 */
function PDFsquidAutoload($dir)
{
    // load and parse composer.json file for PSR-4 namespaces
    $file = $dir . DIRECTORY_SEPARATOR . 'composer.json';
    if (!file_exists($file) || !is_readable($file)) {
        throw new Exception('composer.json is not accessible.');
    }
    $composer = json_decode(file_get_contents($file), 1);
    if (json_last_error() !== JSON_ERROR_NONE || !isset($composer['autoload']['psr-4'])) {
        throw new Exception('composer.json is corrupted. ');
    }
    $namespaces = $composer['autoload']['psr-4'];
    // load desired classes
    foreach ($namespaces as $namespace => $classpath) {
        spl_autoload_register(function ($classname) use ($namespace, $classpath, $dir) {
            if (preg_match('#^' . preg_quote($namespace) . '#', $classname)) {
                $classname = str_replace($namespace, '', $classname);
                $filename = preg_replace("#\\\\#", '/', $classname) . '.php';
                include_once sprintf('%s/%s/%s', $dir, $classpath, $filename);
            }
        }, true, true);
    }
}

PDFsquidAutoload(__DIR__);