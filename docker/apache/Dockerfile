
FROM liu12/apache-php:7.4.33

RUN a2enmod rewrite

RUN mkdir /phpbe-src \
  && cd /phpbe-src \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer \
  && composer create-project be/new /phpbe-src


RUN mkdir /phpbe \
  && cd /phpbe \
  && cp -r /phpbe-src/* /phpbe/ \
  && mkdir -p /phpbe/data \
  && chmod -R 777 /phpbe/data \
  && mkdir -p /phpbe/www \
  && chmod -R 777 /phpbe/www


RUN cat > /phpbe/www/.htaccess <<-EOF
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
EOF


RUN cat > /phpbe/www/index.php <<-EOF
<?php
$rootPath = dirname(__DIR__);
$loader = require($rootPath . '/vendor/autoload.php');
$loader->addPsr4('Be\\Data\\', $rootPath . '/data');

$runtime = new \Be\Runtime\Driver\Common();
$runtime->setRootPath($rootPath);
\Be\Be::setRuntime($runtime);
$runtime->execute();
EOF


RUN cat > /etc/apache2/sites-available/000-default.conf <<-EOF
<VirtualHost *:80>
  DocumentRoot /phpbe/www
</VirtualHost>
<Directory /phpbe>
  Options FollowSymLinks
  AllowOverride All
  Require all granted
</Directory>
<Directory /phpbe/www>
  Options +FollowSymLinks +IncludesNOEXEC -Indexes
  DirectoryIndex index.php index.html index.htm
  AllowOverride All
  Order Deny,Allow
  Allow from all
  php_admin_value open_basedir /phpbe:/tmp
</Directory>
EOF


WORKDIR /phpbe

ADD ./docker/apache/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh

