<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/bootstrap.php';
\App\Core\App::boot(__DIR__ . '/config.testing.php');
\App\Core\Session::start();
