<?php

namespace Drupal\pantheon_secrets\SecretsSyncer;

use PantheonSystems\CustomerSecrets\CustomerSecrets;
use PantheonSystems\CustomerSecrets\CustomerSecretsClientInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Sync secrets from Pantheon to key entities.
 */
class SecretsSyncer implements SecretsSyncerInterface {

  /**
   * The customer secrets client.
   *
   * @var \PantheonSystems\CustomerSecrets\CustomerSecretsClientInterface
   */
  protected CustomerSecretsClientInterface $secretsClient;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new SecretsSyncer object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->secretsClient = CustomerSecrets::create()->getClient();
  }

  /**
   * {@inheritdoc}
   */
  public function sync(): array {
    $secrets = $this->secretsClient->getSecrets();
    $query = $this->entityTypeManager->getStorage('key')->getQuery();
    $key_ids = $query->condition('key_provider', 'pantheon')->execute();
    $keys = $this->entityTypeManager->getStorage('key')->loadMultiple($key_ids);
    $saved = [];
    foreach ($secrets as $secret) {
      if (!$this->secretInUse($secret->getName(), $keys)) {
        // Create and save a new key item only if the secret is not in use.
        $key = $this->entityTypeManager->getStorage('key')->create([
          'id' => $secret->getName(),
          'label' => $secret->getName(),
          'key_provider' => 'pantheon',
          'key_type' => 'authentication',
          'key_provider_settings' => [
            'secret_name' => $secret->getName(),
          ],
        ]);
        $key->save();
        $saved[] = $key->id();
      }
    }
    return $saved;
  }

  /**
   * Determine whether the given secret is in use on any of the given keys.
   */
  protected function secretInUse(string $secretName, array $keys): bool {
    foreach ($keys as $key) {
      $keyPlugin = $key->getPlugin('key_provider');
      if ($keyPlugin->getConfiguration()['secret_name'] === $secretName) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
