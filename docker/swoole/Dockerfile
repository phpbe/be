FROM liu12/swoole:4.8.5-php7.4

RUN mkdir /phpbe-src \
  && cd /phpbe-src \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer \
  && composer create-project be/new /phpbe-src

VOLUME /phpbe

WORKDIR /phpbe

ADD ./docker/swoole/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh
