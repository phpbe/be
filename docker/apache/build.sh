#!/bin/bash

version=7.4.30-apache-buster
docker build --build-arg PHP_VERSION=${version} -t phpbe/be:${version}  .
docker push phpbe/be:${version}

docker tag phpbe/be:${version} phpbe/be:latest
docker push phpbe/be:latest
