#/bin/bash

if [ ! -d "/phpbe/src" ]; then
  cp -r /phpbe-src/* /phpbe/
fi

if [ ! -d "/phpbe/data" ]; then
  mkdir -p /phpbe/data && chmod -R 777 /phpbe/data
fi

if [ ! -d "/phpbe/www" ]; then
  mkdir -p /phpbe/www && chmod -R 777 /phpbe/www
fi

if [ ! -f "/phpbe/www/.htaccess" ]; then
  cat > /phpbe/www/.htaccess <<-EOF
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
EOF
fi

if [ ! -f "/phpbe/www/index.php" ]; then
  cat > /phpbe/www/index.php <<-EOF
<?php
$rootPath = dirname(__DIR__);
$loader = require($rootPath . '/vendor/autoload.php');
$loader->addPsr4('Be\\Data\\', $rootPath . '/data');

$runtime = new \Be\Runtime\Driver\Common();
$runtime->setRootPath($rootPath);
\Be\Be::setRuntime($runtime);
$runtime->execute();
EOF
fi

apache2-foreground