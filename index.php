<?php
$loader = require(__DIR__ . '/vendor/autoload.php');
$loader->addPsr4('Be\\Data\\', __DIR__ . '/data');

$runtime = new \Be\Runtime\Driver\Common();
$runtime->setRootPath(__DIR__);
\Be\Be::setRuntime($runtime);
$runtime->execute();

