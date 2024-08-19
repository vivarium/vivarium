<?php

declare(strict_types=1);

use Vivarium\App\App;

/** Choose how many levels server root is */
define('VIVARIUM_APP_LEVELS', 1);

/** Save current directory as public directory */ 
define('VIVARIUM_APP_PUBLIC', __DIR__);

/** Compute the server root */
define('VIVARIUM_SERVER_ROOT', dirname(VIVARIUM_APP_PUBLIC, VIVARIUM_APP_LEVELS));

/** The path where the core app resides, choose a non public directory where the app will be installed */
define('VIVARIUM_APP_ROOT', join(DIRECTORY_SEPARATOR, [VIVARIUM_SERVER_ROOT, '']));

/** The path where the content resides, choose a non public directory where content will be installed */
define('VIVARIUM_CONTENT_ROOT', join(DIRECTORY_SEPARATOR, [VIVARIUM_APP_ROOT, 'content']));

/** The path where the plugins configurations resides, choose a non public directory where plugins configuration will be installed */
define('VIVARIUM_PLUGINS_ROOT', join(DIRECTORY_SEPARATOR, [VIVARIUM_CONTENT_ROOT, 'plugins']));


try {
    require join(DIRECTORY_SEPARATOR, [VIVARIUM_APP_ROOT, 'vendor', 'autoload.php']);

    (new App())
        ->kernel()
        ->boot();

    exit(0);
} 
catch (Throwable $ex) {
    $error = sprintf(
        '[%s] %s',
        (new DateTime())->format('d/M/Y:H:i:s O'),
        $ex->getMessage()
    );

    if (! is_dir(VIVARIUM_CONTENT_ROOT)) {
        mkdir(VIVARIUM_CONTENT_ROOT);
    }

    $result = file_put_contents(
        join(DIRECTORY_SEPARATOR, [VIVARIUM_CONTENT_ROOT, 'kernel.log']),
        [$error, PHP_EOL],
        FILE_APPEND | LOCK_EX
    );

    if ($result === false) {
        error_log($error);
    }

    header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable', true, 503);

    exit(
        '<h1>Vivarium currently unavailable</h1>' .
        '<h2>Check the log for more informations.</h2>'
    );
}
