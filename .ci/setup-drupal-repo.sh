#!/bin/bash
set -e

# Change base env if drupal version is 10.
if [ "$DRUPAL_VERSION" == "10" ]; then
  export TERMINUS_BASE_ENV=drupal10
fi

if [ "$TERMINUS_BASE_ENV" = "dev" ]; then
  export TERMINUS_BASE_ENV=master
fi

# Configure global composer.
composer config -g github-oauth.github.com $GITHUB_TOKEN

# Bring the code down to Circle so that modules can be added via composer.
git clone $(terminus connection:info ${TERMINUS_SITE}.dev --field=git_url) --branch $TERMINUS_BASE_ENV drupal-site
cd drupal-site

git checkout -b $MULTIDEV_NAME

composer -- config repositories.secrets vcs https://github.com/pantheon-systems/pantheon_secrets.git

export SECRETS_VERSION=$GIT_BRANCH

if [ $GIT_REF_TYPE = "branch" ]; then
  # dev-1.0.x does not match anything, should be 1.0.x-dev as per https://getcomposer.org/doc/articles/aliases.md#branch-alias.
  export BRANCH_PART="dev-${GIT_BRANCH}"
  if [[ $GIT_BRANCH == *".x" ]]; then
    export BRANCH_PART="${GIT_BRANCH}-dev"
  fi

  SECRETS_VERSION="${BRANCH_PART}#${COMMIT_SHA}"
fi

# Composer require the given commit of this module
composer -- require "drupal/pantheon_secrets:${SECRETS_VERSION}"

# Don't commit a submodule
rm -rf web/modules/contrib/pantheon_secrets/.git/

# Make a git commit
git add .
git commit -m 'Result of build step'
