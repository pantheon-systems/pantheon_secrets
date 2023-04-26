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
    try {
      $added = $this->secretsSyncer->sync();
      if (empty($added)) {
        $this->logger()->notice(dt('No new secrets to sync.'));
        return self::EXIT_SUCCESS;
      }
      $this->logger()->success(dt('Synced secrets: @secrets', ['@secrets' => implode(', ', $added)]));
      return self::EXIT_SUCCESS;
    }
    catch (\Exception $e) {
      $this->logger()->error(dt('An error ocurred adding secrets: @error', ['@error' => $e->getMessage()]));
      return self::EXIT_FAILURE;
    }
  }

}
