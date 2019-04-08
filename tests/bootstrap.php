<?php
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../src'),
    get_include_path(),
)));

// Composer autoloader
require realpath(__DIR__ . '/../../../autoload.php');


