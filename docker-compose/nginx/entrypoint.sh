#!/bin/bash

if [ "${NGINX_HTTPS_ENABLED}" = "true" ]; then
  # SSL 证书
  HTTPS_CONFIG=$(envsubst < /etc/nginx/https.conf.template)
else
  HTTPS_CONFIG=''
fi

export HTTPS_CONFIG

env_vars=$(printenv | cut -d= -f1 | sed 's/^/$/g' | paste -sd, -)

envsubst "$env_vars" < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec nginx -g 'daemon off;'