let script = document.createElement('script');
script.src = 'https://maps.googleapis.com/maps/api/js?&libraries=places&key=AIzaSyD5y2Y1zfyWCWDEPRLDBDYuRoJ8ReHYXwY&callback=initMap';
document.head.appendChild(script);

function initMap() { }
(function ($) {
    'use strict';

    $(() => {
        initMap = function () {
            $('#sendy_fulfillment_delivery_address').val('');
        }
    });

    $('#sendy_fulfillment_delivery_address').keyup(function () {
        if (typeof google === 'object' && typeof google.maps === 'object') {
            let country = 'ke';
            let options = {
                componentRestrictions: { country: country },
            };
            let autocomplete = new google.maps.places.Autocomplete($("#sendy_fulfillment_delivery_address")[0], options);
            google.maps.event.addListener(autocomplete, 'place_changed',
                function () {
                    let place = autocomplete.getPlace();
                    let to_name = place.name;
                    let to_lat = place.geometry.location.lat();
                    let to_long = place.geometry.location.lng();
                    saveLocation(to_name, to_lat, to_long);
                });
        } else {
            $.getScript("https://maps.googleapis.com/maps/api/js?&libraries=places&key=AIzaSyD5y2Y1zfyWCWDEPRLDBDYuRoJ8ReHYXwY&callback=initMap");
        }
    });


    function saveLocation(to_name, to_lat, to_long) {
        console.log(to_name,to_lat, to_long)
        $.ajax({
            dataType: 'json',
            url: ajax_object.ajaxurl,
            type: 'post',
            data: {
                'action': 'saveCustomerLocation',
                'to_name': to_name,
                'to_lat': to_lat,
                'to_long': to_long,
            }
        });
    }

})(jQuery);