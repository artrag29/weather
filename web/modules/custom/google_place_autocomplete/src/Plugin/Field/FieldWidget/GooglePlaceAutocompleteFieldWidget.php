<?php

namespace Drupal\google_place_autocomplete\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'google_place_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "google_place_autocomplete_field_widget",
 *   module = "google_place_autocomplete",
 *   label = @Translation("Google Places Autocomplete"),
 *   field_types = {
 *     "text",
 *     "string"
 *   }
 * )
 */
class GooglePlaceAutocompleteFieldWidget extends WidgetBase implements WidgetInterface, ContainerFactoryPluginInterface {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Constructs Field Widget.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   Country Manager Service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, CountryManagerInterface $country_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->countryManager = $country_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('country_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'types' => 'address',
      'country' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $types = [
      '' => $this->t('All'),
      'address' => $this->t('Address'),
      '(cities)' => $this->t('Cities'),
      'establishment' => $this->t('Establishment'),
      'geocode' => $this->t('Geocode'),
      '(regions)' => $this->t('Regions'),
    ];
    $elements['types'] = [
      '#type' => 'radios',
      '#default_value' => $this->getSetting('types'),
      '#options' => $types,
      '#title' => $this->t('The type of results'),
      '#description' => $this->t('The types of predictions to be returned. If nothing is specified, all types are returned.'),
    ];

    $countries = $this->countryManager->getStandardList();
    foreach ($countries as $key => $value) {
      $countries[$key] = $value->__toString();
    }
    $elements['country'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#size' => 10,
      '#title' => $this->t('Country'),
      '#options' => ['' => '- Select Countries -'] + $countries,
      '#default_value' => $this->getSetting('country'),
      '#description' => $this->t('Component restrictions are used to restrict predictions to only those within the parent component. For example, the country.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    // Types.
    if (!empty($this->getSetting('types'))) {
      $summary[] = $this->t('Type of results: @types', ['@types' => $this->getSetting('types')]);
    }

    // Countries.
    if (!empty($this->getSetting('country'))) {
      $summary[] = $this->t('Countries: @country', ['@country' => implode(', ', $this->getSetting('country'))]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => !empty($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#theme' => 'google_place_autocomplete',
      '#attributes' => [
        'data-google-places-autocomplete' => TRUE,
      ],
      '#google_places_options' => [
        'types' => $this->getSetting('types'),
        'country' => $this->getSetting('country'),
      ],
    ];
    return $element;
  }

}
