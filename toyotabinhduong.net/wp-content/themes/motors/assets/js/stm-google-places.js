(function($) {
    "use strict";

    var Places = STMListings.Places = {};

    Places.autocompleteConfig = function () {
    	return {types: ['geocode']};
	};

	Places.addGoogleAutocomplete = function (location_id) {
        var input = document.getElementById(location_id);

        var autocomplete = new google.maps.places.Autocomplete(input, Places.autocompleteConfig());

        //Place changed hook
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            var lat = 0;
            var lng = 0;

            if(typeof place.geometry !== 'undefined') {
                lat = place.geometry.location.lat();
                lng = place.geometry.location.lng();
            }

            $('#' + location_id).closest('.stm-location-search-unit').find('input[name="stm_lat"]').val(lat);
            $('#' + location_id).closest('.stm-location-search-unit').find('input[name="stm_lng"]').val(lng);
        });

        //If user just entered some text, without getting prediction, geocode it
        google.maps.event.addDomListener(input, 'keydown', function(e) {

            var places = autocomplete.getPlace();

            if(typeof(places) == 'undefined') {
                Places.geocoderByInput(location_id);
            } else {
                if(typeof(places.geometry) == 'undefined' || places.name != $('#' + location_id )) {
                    Places.geocoderByInput(location_id);
                }
            }

            if (e.keyCode == 13) {
                e.preventDefault();
            }
        });
    };

	Places.geocoderByInput = function (location_id) {

        var address_search = $('#' + location_id).val();

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': address_search}, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {

                var lat = results[0].geometry.location.lat();
                var lng = results[0].geometry.location.lng();

                $('#' + location_id).closest('.stm-location-search-unit').find('input[name="stm_lat"]').val(lat);
                $('#' + location_id).closest('.stm-location-search-unit').find('input[name="stm_lng"]').val(lng);
            }
        });
    };


	Places.initializePlacesSearch = function () {
        $('.stm_listing_search_location').each(function () {
            var location_id = $(this).attr('id');
            if (typeof(location_id) != 'undefined') {
                Places.addGoogleAutocomplete(location_id);
            }
        });

        if($('#ca_location_listing_filter').length >0) {
            Places.addGoogleAutocomplete('ca_location_listing_filter');
        }

        if($('#stm-add-car-location').length >0) {
            Places.addGoogleAutocomplete('stm-add-car-location');
        }

        if($('#stm_google_user_location_entry').length >0) {
            Places.addGoogleAutocomplete('stm_google_user_location_entry');
        }
    };


	Places.initAsync = function () {
        google.maps.event.addDomListener(window, 'load', Places.initializePlacesSearch);
    };


	Places.reverseGeocoder = function (lat, lng) {
        var geocoder = new google.maps.Geocoder;
        var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === 'OK') {
                if (results[1]) {
                    $("input[name='stm_location_text']").val(results[0].formatted_address.trim());
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    };

    $(document).ready(function(){

        Places.initAsync();

        var cur_val = $('#ca_location_listing_filter').val();
        if(cur_val == '') {
            $('#ca_location_listing_filter').addClass('empty');
        } else {
            $('#ca_location_listing_filter').removeClass('empty');
        }

        $('#ca_location_listing_filter').keyup(function(){
            if($(this).val() == '') {
                $(this).addClass('empty');
            } else {
                $(this).removeClass('empty');
            }
        });

        $(".text_stm_lat").on("keyup", function() {
            var lng = $(".text_stm_lng").val();
            if($(this).val().length > 0 && lng.length > 0) Places.reverseGeocoder($(this).val(), lng);
        });

        $(".text_stm_lng").on("keyup", function() {
            var lat = $(".text_stm_lat").val();

            if($(this).val().length > 0 && lat.length > 0) Places.reverseGeocoder(lat, $(this).val());
        });
    })

})(jQuery);