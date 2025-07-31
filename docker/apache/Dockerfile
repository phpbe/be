
FROM liu12/apache-php:7.4.33

RUN a2enmod rewrite

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


RUN mkdir /phpbe-src \
  && cd /phpbe-src \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer \
  && composer create-project be/new /phpbe-src

VOLUME /phpbe

WORKDIR /phpbe

ADD ./docker/apache/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh

