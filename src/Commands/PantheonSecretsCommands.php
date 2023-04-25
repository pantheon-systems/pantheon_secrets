<?php

namespace Drupal\pantheon_secrets\Commands;

use Drush\Commands\DrushCommands;
use Drupal\pantheon_secrets\SecretsSyncer\SecretsSyncerInterface;

/**
 * Drush command file.
 */
class PantheonSecretsCommands extends DrushCommands {

  /**
   * The secrets syncer.
   *
   * @var \Drupal\pantheon_secrets\SecretsSyncer\SecretsSyncerInterface
   */
  protected SecretsSyncerInterface $secretsSyncer;

  /**
   * PantheonSecretsCommands constructor.
   */
  public function __construct(SecretsSyncerInterface $secrets_syncer) {
    $this->secretsSyncer = $secrets_syncer;
  }

  /**
   * Sync Pantheon secrets with key entities.
   *
   * @command pantheon-secrets:sync
   */
  public function sync() {
    $success = $this->secretsSyncer->sync();
    if ($success) {
      $this->logger()->success(dt('Pantheon secrets synced successfully.'));
    }
    else {
      $this->logger()->error(dt('Pantheon secrets sync failed.'));
    }
  }
}
