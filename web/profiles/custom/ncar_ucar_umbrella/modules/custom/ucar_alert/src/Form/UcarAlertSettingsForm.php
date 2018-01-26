<?php

namespace Drupal\ucar_alert\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class UcarAlertSettingsForm extends ConfigFormBase {

  protected $settings_id = 'ucar_alert.settings';

  protected function getEditableConfigNames() {
    return [
      $this->settings_id,
    ];
  }

  public function getFormId() {
    return 'ucar_alert_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config($this->settings_id);

    $form['alert_api'] = array(
      '#type' => 'url',
      '#title' => $this->t('Alert API'),
      '#default_value' => $config->get('url'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::configFactory()->getEditable($this->settings_id)
      ->set('url', $form_state->getValue('alert_api'))->save();

    parent::submitForm($form, $form_state);
  }
}