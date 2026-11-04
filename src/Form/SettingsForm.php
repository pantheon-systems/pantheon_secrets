<?php

namespace Drupal\pantheon_secrets\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

    public function getFormId() {
        return 'pantheon_secrets.settings';
    }

    protected function getEditableConfigNames() {
        return ['pantheon_secrets.settings'];
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['pantheon_secrets'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Pantheon Secrets'),
            '#description' => $this->t('Configure the Pantheon Secrets module.'),
        ];

        $form['pantheon_secrets']['pantheon_secrets_sync'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Automatically Sync Pantheon Secrets'),
            '#description' => $this->t('Sync Pantheon Secrets to Key entities on every cron run.'),
            '#default_value' => $this->config('pantheon_secrets.settings')->get('pantheon_secrets_sync'),
        ];

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->config('pantheon_secrets.settings')
            ->set('pantheon_secrets_sync', $form_state->getValue('pantheon_secrets_sync'))
            ->save();

        parent::submitForm($form, $form_state);
    }

}