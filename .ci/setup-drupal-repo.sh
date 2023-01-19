#!/bin/bash
set -e

# Change base env if drupal version is 10.
if [ "$DRUPAL_VERSION" == "10" ]; then
  export TERMINUS_BASE_ENV=drupal10
fi

if [ "$TERMINUS_BASE_ENV" = "dev" ]; then
  export TERMINUS_BASE_ENV=master
fi

# Bring the code down to Circle so that modules can be added via composer.
git clone $(terminus connection:info ${TERMINUS_SITE}.dev --field=git_url) --branch $TERMINUS_BASE_ENV drupal-site
cd drupal-site

git checkout -b $MULTIDEV_NAME

composer -- config repositories.secrets vcs https://github.com/pantheon-systems/pantheon_secrets.git

# dev-1.x does not match anything, should be 1.x-dev as per https://getcomposer.org/doc/articles/aliases.md#branch-alias.
export BRANCH_PART="dev-${GIT_BRANCH}"
if [ $GIT_BRANCH = "1.x" ]; then
  export BRANCH_PART="1.x-dev"
fi
# Composer require the given commit of this module
composer -- require "drupal/pantheon_secrets:${BRANCH_PART}#${COMMIT_SHA}"

# Don't commit a submodule
rm -rf web/modules/contrib/pantheon_secrets/.git/

# Make a git commit
git add .
git commit -m 'Result of build step'
