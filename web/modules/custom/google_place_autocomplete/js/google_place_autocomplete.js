(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.google_place_autocomplete = {
    attach: function (context, settings) {
      if (!settings.google_place_autocomplete) {
        return;
      }

      // Initialize Autocomplete fields.
      function initializeAutocomplete() {
        // Loop over autocomplete fields
        $(once('google_place_autocomplete', 'input[data-google-places-autocomplete]', context)).each(function () {
          let input = $(this);
          input.addClass('processed-autocomplete');
          // Setup options
          let options = {
            fields: ['address_component', 'formatted_address', 'geometry'],
          };
          if (settings.google_place_autocomplete.elements[input.attr('id')]) {
            if (settings.google_place_autocomplete.elements[input.attr('id')].options.types) {
              options['types'] = [settings.google_place_autocomplete.elements[input.attr('id')].options.types];
            }
            if (settings.google_place_autocomplete.elements[input.attr('id')].options.country) {
              options['componentRestrictions'] = {country: settings.google_place_autocomplete.elements[input.attr('id')].options.country};
            }
          }
          let autocomplete = new google.maps.places.Autocomplete(this, options);

          autocomplete.addListener('place_changed', function () {
            let place = autocomplete.getPlace();
            if (!place.geometry) {
              // User entered the name of a place that was not suggested and pressed the Enter key,
              // or the Place Details request failed.
              window.alert("No details available for input: '" + place.name + "'");
              return;
            }
            $('#edit-location-lat', context).val(place.geometry.location.lat());
            $('#edit-location-lng', context).val(place.geometry.location.lng());

          });
        });
      }

      window.addEventListener('load', initializeAutocomplete);
      $(document).ajaxStop(initializeAutocomplete);
    }
  };
})(jQuery, Drupal);
