(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.google_place_autocomplete = {
    attach: function (context, settings) {
      if (!settings.google_place_autocomplete) {
        return;
      }
      let suggestionSelected = false;

      // Initialize Autocomplete fields.
      function initializeAutocomplete() {
        // Loop over autocomplete fields
        $(once('google_place_autocomplete', 'input[data-google-places-autocomplete]', context)).each(function () {
          let input = $(this);
          // Reset the flag on input change
          input.on('input', function() {
            suggestionSelected = false;
          });
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
            // Set the flag to true when a place is selected
            suggestionSelected = true;
            if (!place.geometry) {
              window.alert("No details available for input: '" + place.name + "'");
              return;
            }
            $('#edit-location-lat', context).val(place.geometry.location.lat());
            $('#edit-location-lng', context).val(place.geometry.location.lng());

          });
        });
      }
      // Check the flag before setting lat and lng
      $(document).ajaxStop(function() {
        if (!suggestionSelected) {
          $('#edit-location-lat', context).val(null);
          $('#edit-location-lng', context).val(null);
        }
        initializeAutocomplete();
      });
      window.addEventListener('load', initializeAutocomplete);
      $(document).ajaxStop(initializeAutocomplete);
    }
  };
})(jQuery, Drupal);
