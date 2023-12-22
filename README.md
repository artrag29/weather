# Weather Info Module

## Overview
In the development of a custom weather information module, I employed the OpenWeatherMap API to retrieve and display real-time weather data. A key feature of the module is the integration of the Google Places Autocomplete functionality, which was incorporated by moving an existing module to the custom module directory and applying necessary JavaScript adjustments for Drupal 10 compatibility. These alterations included several fixes to refine the module's performance and ensure accurate city to latitude and longitude conversions, a requirement for the OpenWeatherMap API's endpoint parameters.

## Setting Up a Basic Drupal Site
 **Install Dependencies with Composer**:
  - Navigate to root directory in the command line.
  - Run `composer install` to install the required dependencies.

## Configuring the Weather Info Module
To streamline the setup process, I created configuration installation YML files with predefined settings. Upon enabling the module, these configurations are automatically populated, providing a hassle-free initial setup and ensuring that all necessary API keys and settings are in place.

## Installation
1. **Enable the Module**: Navigate to the Extend section of your Drupal site and enable the Weather Info Module.
2. **Configure the Module**: Access the module's configuration page to customize settings as per your requirements.
