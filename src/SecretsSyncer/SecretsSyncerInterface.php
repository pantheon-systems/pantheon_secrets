<?php

namespace Drupal\pantheon_secrets\SecretsSyncer;

/**
 * Interface for the service that syncs secrets.
 */
interface SecretsSyncerInterface {

  /**
   * Sync secrets from Pantheon to key entities.
   */
  public function sync(): bool;

}
