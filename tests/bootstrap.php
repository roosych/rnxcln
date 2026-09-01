<?php

/*
| docker-compose.yml's app service injects the real DB_* connection details
| as container-level environment variables (so .env can read them at
| runtime). Those same variables land in $_SERVER at process start, and
| PHPUnit's <env force="true"> overrides in phpunit.xml only ever touch
| $_ENV/putenv() — never $_SERVER — while Laravel's env()/config()
| resolution checks $_SERVER first. Net effect: tests kept resolving the
| REAL mariadb connection no matter what phpunit.xml or .env.testing said,
| and a RefreshDatabase test wiped the real dev database.
|
| Scrubbing these from $_SERVER before Laravel ever boots (autoload hasn't
| even run yet at this point) removes that shadow, so phpunit.xml's <env>
| block and .env.testing are free to set the test values.
*/
foreach (['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $key) {
    unset($_SERVER[$key]);
}

require __DIR__.'/../vendor/autoload.php';
