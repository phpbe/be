#/bin/bash

if [ ! -d "/phpbe/vendor" ]; then
  cp -r /phpbe-src/* /phpbe/
  chmod -R 777 /phpbe/data
  chmod -R 777 /phpbe/www
fi

php server.php start
