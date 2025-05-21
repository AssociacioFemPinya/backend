#!/usr/bin/env bash

echo Resetting file ownership within the container. This might take a while...
chown --silent -R 1000:1000 /var/www/html || echo Done.
