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

php server.php start
