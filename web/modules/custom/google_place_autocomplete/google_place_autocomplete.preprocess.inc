<?php

/**
 * @file
 * Contains google_place_autocomplete.preprocess.inc.
 */

/**
 * Prepares variables for Google Places autocomplete input field.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_google_place_autocomplete(array &$variables) {
  if (!empty($variables['element']['#id'])) {
    $element = $variables['element'];
    $element_id = $element['#id'];
    $variables['#attached']['drupalSettings']['google_place_autocomplete']['elements'][$element_id] = [
      'options' => $element['#google_places_options'],
    ];
  }
}
