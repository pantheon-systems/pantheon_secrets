Pantheon Secrets
=================

Pantheon Secrets integration with the [Key](https://drupal.org/project/key) module.

[![Pantheon Secrets](https://github.com/pantheon-systems/pantheon_secrets/actions/workflows/ci.yml/badge.svg?branch=1.x)](https://github.com/pantheon-systems/pantheon_secrets/actions/workflows/ci.yml)

[![Limited Availability](https://img.shields.io/badge/Pantheon-Limited_Availability-yellow?logo=pantheon&color=FFDC28)](https://pantheon.io/docs/oss-support-levels#limited-availability)

## Requirements

This module is for you if you meet the following requirements:

* Using Drupal 9.4/10

* Part of the Secrets EA Program

* Hosting the Drupal site on Pantheon's platform

* Your site uses `composer` to install modules and upgrade Drupal core using one of the following integrations:

  * Pantheon's integrated composer (`build step: true` in your pantheon.yml)

  * A Continuous Integration service like Circle CI or Travis

* Have Dashboard access to the platform (necessary to deploy code changes)

* Comfortable using [terminus](https://pantheon.io/docs/terminus)

* Using the [Secrets Manager Plugin](https://github.com/pantheon-systems/terminus-secrets-manager-plugin) to set your secrets.


## What it provides

This module provides [Drupal](https://drupal.org) integration with the Secrets EA Program in the form of a Key Provider plugin for the [Key](https://drupal.org/project/key) module.

## Install

To require this module in your composer file:

```
composer require pantheon-systems/pantheon_secrets:^1 --prefer-dist
```

Install the module and push an updated `composer.lock` file to your Pantheon environment.

## Setup

1) Use terminus to set some secrets like this:

    ```
    terminus secret:set <site> --scope=web <secret_name> <secret_value>
    ```

1) Add a new key through the Key module UI. Select Pantheon Secret as the key provider
1) Use the key where it is needed.