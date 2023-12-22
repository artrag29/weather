<?php

namespace Drupal\weather_info;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;


class WeatherInfoService {

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected CacheBackendInterface $cacheBackend;
  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  public function __construct(
    ClientInterface $http_client,
    ConfigFactoryInterface $config_factory,
    CacheBackendInterface $cache_backend,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
    $this->cacheBackend = $cache_backend;
    $this->logger = $logger_factory->get('weather_info');
  }

  /**
   * @param $lat
   * @param $lon
   *
   * @return array|null
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getWeatherData($lat, $lon): ?array {
    $cache_id = 'weather_info:data:' . $lat . ':' . $lon;
    $cached = $this->cacheBackend->get($cache_id);

    // If data is in the cache and not expired, return it.
    if ($cached) {
      return $cached->data;
    }

    $config = $this->configFactory->get('weather_info.settings');
    $apiKey = $config->get('api_key');
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

    try {
      $response = $this->httpClient->request('GET', $url);
      $data = Json::decode($response->getBody());
      $processed_data = $this->processWeatherData($data);

      // Cache the processed data for an hour (3600 seconds).
      $this->cacheBackend->set($cache_id, $processed_data, time() + 3600);

      return $processed_data;
    } catch (RequestException $e) {
      $this->logger->error($e->getMessage());
      return NULL;
    }
  }

  /**
   * @param $api_response
   *
   * @return array
   */
  private function processWeatherData($api_response): array {
    $processed_data = [];

    if (isset($api_response['main'])) {
      $processed_data['temperature'] = $api_response['main']['temp'];
      $processed_data['humidity'] = $api_response['main']['humidity'];
    }

    if (isset($api_response['weather'][0])) {
      $processed_data['description'] = $api_response['weather'][0]['description'];
      $processed_data['icon'] = $api_response['weather'][0]['icon'];

    }

    return $processed_data;
  }
}
