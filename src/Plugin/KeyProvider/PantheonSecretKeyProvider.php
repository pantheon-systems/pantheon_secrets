<?php

namespace Drupal\pantheon_secrets\Plugin\KeyProvider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyProviderBase;
use Drupal\key\Plugin\KeyPluginFormInterface;
use Drupal\key\KeyInterface;
use PantheonSystems\CustomerSecrets\CustomerSecrets;
use PantheonSystems\CustomerSecrets\CustomerSecretsClientInterface;

/**
 * A key provider that allows a key to be retrieved from Pantheon secrets.
 *
 * @KeyProvider(
 *   id = "pantheon_secret",
 *   label = @Translation("Pantheon Secret"),
 *   description = @Translation("The Pantheon Secret key provider allows a key to be retrieved from a pantheon secret."),
 *   storage_method = "pantheon_secret",
 *   key_value = {
 *     "accepted" = FALSE,
 *     "required" = FALSE
 *   }
 * )
 */
class PantheonSecretKeyProvider extends KeyProviderBase implements KeyPluginFormInterface {

  /**
   * The customer secrets client.
   *
   * @var \PantheonSystems\CustomerSecrets\CustomerSecretsClientInterface
   */
  protected CustomerSecretsClientInterface $secretsClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->secretsClient = CustomerSecrets::create()->getClient();
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'secret_name' => '',
      'strip_line_breaks' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['secret_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret name'),
      '#description' => $this->t('Name of the secret set in Pantheon.'),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['secret_name'],
    ];

    $form['strip_line_breaks'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Strip trailing line breaks'),
      '#description' => $this->t('Check this to remove any trailing line breaks from the variable. Leave unchecked if there is a chance that a line break could be a valid character in the key.'),
      '#default_value' => $this->getConfiguration()['strip_line_breaks'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $key_provider_settings = $form_state->getValues();
    $secret_name = $key_provider_settings['secret_name'];
    $secret_value = $this->secretsClient->getSecret($secret_name);

    // Does the secret exist.
    if (!$secret_value) {
      $form_state->setErrorByName('secret_name', $this->t('The secret does not exist or it is empty.'));
      return;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValues());
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key) {
    $secret_name = $this->configuration['secret_name'];
    $secret = $this->secretsClient->getSecret($secret_name);

    if (!$secret) {
      return NULL;
    }

    $secret_value = $secret->getValue();

    if (isset($this->configuration['strip_line_breaks']) && $this->configuration['strip_line_breaks'] == TRUE) {
      $secret_value = rtrim($secret_value, "\n\r");
    }

    return $secret_value;
  }

}
