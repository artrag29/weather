<?php

namespace Drupal\google_place_autocomplete\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\Utility\LinkGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Google Places Autocomplete Settings Form.
 *
 * @package Drupal\google_place_autocomplete\Form
 */
class GooglePlaceAutocompleteSettingsForm extends ConfigFormBase {

  /**
   * The state keyvalue collection.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * The link generator service variable.
   *
   * @var \Drupal\Core\Utility\LinkGenerator
   */
  protected $linkGenerator;

  /**
   * Constructs GooglePlaceAutocompleteSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   State Service Object.
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   Country Manager Service.
   * @param \Drupal\Core\Utility\LinkGenerator $link_generator
   *   Link Generator Service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state, CountryManagerInterface $country_manager, LinkGenerator $link_generator) {
    parent::__construct($config_factory);
    $this->state = $state;
    $this->countryManager = $country_manager;
    $this->linkGenerator = $link_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('state'),
      $container->get('country_manager'),
      $container->get('link_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'google_place_autocomplete.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'place_autocomplete_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('google_place_autocomplete.settings');

    $google_api = Url::fromUri('https://developers.google.com/maps/documentation/javascript/get-api-key', [
      'attributes' => ['target' => '_blank'],
    ]);
    $api_link = $this->linkGenerator->generate($this->t('Click here'), $google_api);

    $form['place_autocomplete'] = [
      '#type' => 'details',
      '#title' => $this->t('Google Places Autocomplete settings'),
      '#open' => TRUE,
    ];

    $form['place_autocomplete']['api_key_name'] = [
      '#type' => 'key_select',
      '#title' => $this->t('Google Maps API Key'),
      '#empty_option' => $this->t('- Select Key -'),
      '#default_value' => $config->get('api_key_name'),
      '#key_filters' => ['type' => 'authentication'],
      '#description' => $this->t('A API key is needed to use the Google Maps. @click here to generate the API key', [
        '@click' => $api_link,
      ]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('google_place_autocomplete.settings')
      ->set('api_key_name', $form_state->getValue('api_key_name'))
      ->save();
  }

}
