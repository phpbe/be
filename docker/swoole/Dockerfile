
ARG SWOOLE_VERSION=4.8.5-php7.4

FROM phpswoole/swoole:${SWOOLE_VERSION}

RUN apt-get update && apt-get install -y zip libaio1 procps net-tools iputils-ping vim

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
imagick \
# imap \
# interbase \
# intl \
# ldap \
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
# mysqli \
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
# pdo_sqlsrv  \
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
# soap \
# sockets \
# solr \
# sqlsrv \
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
uuid \
# wddx \
# xdebug \
# xhprof \
# xmlrpc \
# xsl \
# yaml \
zip
# zookeeper


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


WORKDIR /phpbe

ADD ./docker/swoole/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh
ENTRYPOINT php server.php start
