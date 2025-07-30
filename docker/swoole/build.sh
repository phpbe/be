#!/bin/bash

version=4.8.5-php7.4
docker build -t phpbe/be:${version}  .
docker push phpbe/be:${version}