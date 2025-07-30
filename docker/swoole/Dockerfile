FROM liu12/swoole:4.8.5-php7.4

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
