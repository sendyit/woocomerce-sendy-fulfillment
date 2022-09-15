/* eslint-disable */
let script = document.createElement('script');
script.src = 'https://maps.googleapis.com/maps/api/js?&libraries=places&key=AIzaSyD5y2Y1zfyWCWDEPRLDBDYuRoJ8ReHYXwY&callback=initMap';
document.head.appendChild(script);

(function ($) {
    'use strict';
    $('#sendy_fulfillment_pickup_address').keyup(function () {
        if (typeof google === 'object' && typeof google.maps === 'object') {
            $('#sendy_fulfillment_pickup_address_lat').val('');
            $('#sendy_fulfillment_pickup_address_long').val('');
            let country = ['ke', 'ug', 'ng', 'ci'];
            let options = {
                componentRestrictions: { country: country },
            };
            let autocomplete = new google.maps.places.Autocomplete($("#sendy_fulfillment_pickup_address")[0], options);
            google.maps.event.addListener(autocomplete, 'place_changed',
                function () {
                    let place = autocomplete.getPlace();
                    let to_name = place.name;
                    let to_lat = place.geometry.location.lat();
                    let to_long = place.geometry.location.lng();

                    document.getElementById("sendy_fulfillment_pickup_address_lat").value = place.geometry.location.lat();
                    document.getElementById("sendy_fulfillment_pickup_address_long").value = place.geometry.location.lng();
                });
        } else {
            $.getScript("https://maps.googleapis.com/maps/api/js?&libraries=places&key=AIzaSyD5y2Y1zfyWCWDEPRLDBDYuRoJ8ReHYXwY&callback=initMap");
        }

    });

})(jQuery);
