#!/bin/bash

# Change base env if drupal version is 10.
if [ "$DRUPAL_VERSION" == "10" ]; then
  export TERMINUS_BASE_ENV=drupal10
fi

terminus env:create ${TERMINUS_SITE}.${TERMINUS_BASE_ENV} ${MULTIDEV_NAME}
if [ $? -ne 0 ]; then
  echo "Failed to create multidev environment"
  exit 1
fi
touch multidev-made.txt
