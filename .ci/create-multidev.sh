#!/bin/bash

terminus env:create $TERMINUS_SITE.$TERMINUS_BASE_ENV ${GITHUB_RUN_NUMBER}
if [ $? -ne 0 ]; then
  echo "Failed to create multidev environment"
  exit 1
fi
touch multidev-made.txt
