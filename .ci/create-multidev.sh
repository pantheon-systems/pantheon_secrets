#!/bin/bash

terminus env:create $TERMINUS_SITE.$TERMINUS_BASE_ENV ${GITHUB_RUN_NUMBER}
touch multidev-made.txt
