ARG PHP_VERSION=7.4.30-apache-buster

FROM php:${PHP_VERSION}

############################## 默认已安装的扩展
# Core
# ctype
# curl
# date
# dom
# fileinfo
# filter
# ftp
# hash
# iconv
# json
# libxml
# mbstring
# mysqlnd
# openssl
# pcre
# PDO
# pdo_sqlite
# Phar
# posix
# readline
# Reflection
# session
# SimpleXML
# sodium
# SPL
# sqlite3
# standard
# tokenizer
# xml
# xmlreader
# xmlwriter
# zlib

RUN apt-get update && apt-get install -y zip libaio1

# 使用 php docker 推荐的 php-extension-installer 安装扩展
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

############################## 根据需要启用要安装的扩展
RUN install-php-extensions \
# amqp \
# apcu \
# apcu_bc \
bcmath \
# bz2 \
# calendar \
# cmark \
# dba \
# decimal \
# enchant \
# exif \
# >=php7.4
# ffi \
gd \
gettext \
# gmagick \# 
# gmp \
# grpc \
# http \
# igbinary \
# imagick \
# imap \
# interbase \
intl \
ldap \
# mailparse \
mcrypt \
# memcache \
# memcached \
# mongo \
# mongodb \
# msgpack \
# <=php5.6
# mssql \
# <=php5.6
# mysql \
mysqli \
# oauth \
# odbc \
# opcache \
# opencensus \
# parallel \
# pcntl \
# pcov \
# pdo_dblib \
# pdo_firebird \
pdo_mysql \
# pdo_odbc \
# pdo_pgsql \
# <=php7.4
pdo_sqlsrv  \
# pgsql \
# propro \
# protobuf \
# pspell \
# <=php7.0
# pthreads \
# raphf \
# <=php7.4
# rdkafka \
# <=php7.3
# recode \
redis \
# shmop \
# snmp \
# snuffleupagus \
soap \
sockets \
# solr \
sqlsrv \
# ssh2 \
# <=php5.6
# sybase_ct \
# sysvmsg \
# sysvsem \
# sysvshm \
# tdlib \
# tidy \
# timezonedb \
# uopz \
# uuid \
# wddx \
# xdebug \
# xhprof \
# xmlrpc \
# xsl \
# yaml \
zip
# zookeeper



############################## oci8 pdo_oci

RUN cd /tmp \
	&& curl -L https://download.oracle.com/otn_software/linux/instantclient/199000/instantclient-basiclite-linux.x64-19.9.0.0.0dbru.zip -O \
	&& curl -L https://download.oracle.com/otn_software/linux/instantclient/199000/instantclient-sdk-linux.x64-19.9.0.0.0dbru.zip -O \
	&& unzip /tmp/instantclient-basiclite-linux.x64-19.9.0.0.0dbru.zip -d /usr/local/ \
	&& unzip /tmp/instantclient-sdk-linux.x64-19.9.0.0.0dbru.zip -d /usr/local/ \
	&& ln -s /usr/local/instantclient_19_9 /usr/local/instantclient \
	&& ln -s /usr/local/instantclient/lib* /usr/lib \
	&& rm -rf /tmp/*.zip

RUN echo 'export LD_LIBRARY_PATH="/usr/local/instantclient"' >> /root/.bashrc && echo 'umask 002' >> /root/.bashrc

RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8-2.2.0
RUN docker-php-ext-enable oci8

# 安装pdo_oci
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/local/instantclient_19_9,19.9 && docker-php-ext-install pdo_oci



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

  ErrorLog ${APACHE_LOG_DIR}/error.log
  CustomLog ${APACHE_LOG_DIR}/access.log combined
  
  <Directory /phpbe>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>
  
  <Directory "/phpbe/www">
    Options +FollowSymLinks +IncludesNOEXEC -Indexes
    DirectoryIndex index.php index.html index.htm
    AllowOverride All
    Order Deny,Allow
    Allow from all
    php_admin_value open_basedir /phpbe:/tmp
  </Directory>
  
</VirtualHost>
EOF


WORKDIR /phpbe

ADD entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh

