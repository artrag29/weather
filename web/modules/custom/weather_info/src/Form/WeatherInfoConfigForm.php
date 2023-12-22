<?php

namespace Drupal\weather_info\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class WeatherInfoConfigForm extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'weather_info.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'weather_info_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('weather_info.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OpenWeatherMap API Key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
      '#description' => $this->t('Enter your OpenWeatherMap API key here.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('weather_info.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
