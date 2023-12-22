<?php

namespace Drupal\weather_info\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\weather_info\WeatherInfoService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CityCoordinatesForm extends FormBase {

  /**
   * The weather info service.
   *
   * @var \Drupal\weather_info\WeatherInfoService
   */
  protected WeatherInfoService $weatherInfoService;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected RendererInterface $renderer;

  /**
   * Constructs a new CityCoordinatesForm.
   *
   * @param \Drupal\weather_info\WeatherInfoService $weather_info_service
   *   The weather info service.
   */
  public function __construct(WeatherInfoService $weather_info_service, RendererInterface $renderer) {
    $this->weatherInfoService = $weather_info_service;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('weather_info.weather_service'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'weather_info_city_coordinates_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['location'] = [
      '#title' => $this->t('City'),
      '#placeholder' => $this->t('Enter city name'),
      '#type' => 'textfield',
      '#theme' => 'google_place_autocomplete',
      '#attributes' => [
        'data-google-places-autocomplete' => TRUE,
      ],
      '#google_places_options' => [
        'types' => '(cities)',
        'country' => [],
      ],
      '#attached' => [
        'library' => [
          'google_place_autocomplete/google_place_autocomplete',
        ],
      ],
      '#default_value' => '',
    ];
    // Hidden fields for latitude and longitude
    $form['location_lat'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'edit-location-lat'],
    ];

    $form['location_lng'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'edit-location-lng'],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Get current weather'),
      '#ajax' => [
        'callback' => '::weatherInfoAjaxSubmit',
        'wrapper' => 'weather-info-results',
      ],
    ];

    // Add a container to display the results
    $form['results'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'weather-info-results'],
      '#theme' => 'weather_info',
      '#weather_data' => [],
    ];
    $form['#attached']['library'][] = 'weather_info/weather-info-style';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   * @throws \Exception
   */
  public function weatherInfoAjaxSubmit(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();

    $latitude = $form_state->getValue('location_lat');
    $longitude = $form_state->getValue('location_lng');

    // Get weather information using your service
    $weather_data = $this->weatherInfoService->getWeatherData($latitude, $longitude);

    $form['results'] = [
      '#theme' => 'weather_info',
      '#weather_data' => $weather_data, // Processed weather data
    ];
    $response->addCommand(new HtmlCommand('#weather-info-results', $this->renderer->render($form['results'])));

    return $response;
  }

}
