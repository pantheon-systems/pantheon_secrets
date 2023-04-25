<?php

namespace Drupal\pantheon_secrets\SecretsSyncer;

/**
 * Interface SecretsSyncerInterface.
 *
 * @package Drupal\pantheon_secrets\SecretsSyncer
 */
interface SecretsSyncerInterface {

    /**
     * Sync secrets from Pantheon to key entities.
     */
    public function sync(): bool;

}