<?php

// no direct access
defined('_JEXEC') or die;
//require_once dirname(__FILE__).'/helper.php';

$costokm = $params->get('eurokm');

$document = & JFactory::getDocument();
$document->addScript('http://maps.google.com/maps/api/js?sensor=true');

$document->addScriptDeclaration("
	var location1;
	var location2;

	var address1;
	var address2;

	var latlng;
	var geocoder;
	var map;

	var line;

	var infowindow1;
	var infowindow2;

	var distance;

	function initialize(){
            geocoder = new google.maps.Geocoder();

            address1 = document.getElementById(\"address1\").value;
            address2 = document.getElementById(\"address2\").value;

            if (geocoder){
                geocoder.geocode( { 'address': address1}, function(results, status){
                    if (status == google.maps.GeocoderStatus.OK){
                        location1 = results[0].geometry.location;
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
            });

            geocoder.geocode( { 'address': address2}, function(results, status){
                if (status == google.maps.GeocoderStatus.OK){
                    location2 = results[0].geometry.location;
                    showMap();
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });

        }
    }

    function showMap(){
        latlng = new google.maps.LatLng((location1.lat()+location2.lat())/2,(location1.lng()+location2.lng())/2);
        var maptype = document.getElementById('maptype').value;
	var typeId;

        if (maptype == 'roadmap')
            typeId = google.maps.MapTypeId.ROADMAP;
        else if (maptype == 'hybrid')
            typeId = google.maps.MapTypeId.HYBRID;
        else if (maptype == 'satellite')
            typeId = google.maps.MapTypeId.SATELLITE;
        else if (maptype == 'terrain')
            typeId = google.maps.MapTypeId.TERRAIN;

        var mapOptions = {
            zoom: 1,
            center: latlng,
            mapTypeId: typeId
	};

        map = new google.maps.Map(document.getElementById(\"map_canvas\"), mapOptions);

        google.maps.event.addListener(map, 'maptypeid_changed', function() {
            maptype = map.getMapTypeId();
            document.getElementById('maptype').value = maptype;
        });

        var rabbit = new google.maps.MarkerImage('distance-finder-custom-marker-image.png');

        var marker1 = new google.maps.Marker({
            map: map,
            position: location1,
            title: 'First location',
            icon: rabbit,
            draggable: true
        });

        var marker2 = new google.maps.Marker({
                map: map,
                position: location2,
                title: 'Second location',
                icon: rabbit,
                draggable: true
        });

        var text1 = '<div id=\"content\">'+
                        '<h1 id=\"firstHeading\">First location</h1>'+
                        '<div id=\"bodyContent\">'+
                        '<p>Coordinates: '+location1+'</p>'+
                        '<p>Address: '+address1+'</p>'+
                        '</div>'+
                        '</div>';

        var text2 = '<div id=\"content\">'+
                '<h1 id=\"firstHeading\">Second location</h1>'+
                '<div id=\"bodyContent\">'+
                '<p>Coordinates: '+location2+'</p>'+
                '<p>Address: '+address2+'</p>'+
                '</div>'+
                '</div>';

        infowindow1 = new google.maps.InfoWindow({
            content: text1
        });
        infowindow2 = new google.maps.InfoWindow({
            content: text2
        });

        google.maps.event.addListener(marker1, 'click', function() {
            infowindow1.open(map,marker1);
        });
        google.maps.event.addListener(marker2, 'click', function() {
            infowindow2.open(map,marker2);
        });

        google.maps.event.addListener(marker1, 'dragend', function() {
            location1 = marker1.getPosition();
            drawRoutes(location1, location2);
        });

        google.maps.event.addListener(marker2, 'dragend', function() {
            location2 = marker2.getPosition();
            drawRoutes(location1, location2);
        });

        directionsService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            suppressInfoWindows: true
        });

        directionsDisplay.setMap(map);

        drawRoutes(location1, location2);
    }

    function drawRoutes(location1, location2){

        geocoder = new google.maps.Geocoder();
        if (geocoder){
            geocoder.geocode({'latLng': location1}, function(results, status){
                if (status == google.maps.GeocoderStatus.OK){
                    if (results[0]){
                        address1 = results[0].formatted_address;
                        document.getElementById(\"address1\").value = address1;
                    }
                } else {
                    alert(\"Geocoder failed due to: \" + status);
				}
                });
            }

            if (geocoder){
                geocoder.geocode({'latLng': location2}, function(results, status){
                    if (status == google.maps.GeocoderStatus.OK){
                        if (results[0]){
                            address2 = results[0].formatted_address;
                            document.getElementById(\"address2\").value = address2;
                            continueShowRoute(location1, location2);
                        }
                    } else {
                        alert(\"Geocoder failed due to: \" + status);
                    }
                });
            }
	}

	function continueShowRoute(location1, location2){
            if (line){
                line.setMap(null);
            }

            line = new google.maps.Polyline({
                    map: map,
                    path: [location1, location2],
                    strokeWeight: 7,
                    strokeOpacity: 0.8,
                    strokeColor: '#FFAA00'
            });

            var R = 6371;
            var dLat = toRad(location2.lat()-location1.lat());
            var dLon = toRad(location2.lng()-location1.lng());

            var dLat1 = toRad(location1.lat());
            var dLat2 = toRad(location2.lat());

            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                            Math.cos(dLat1) * Math.cos(dLat1) *
                            Math.sin(dLon/2) * Math.sin(dLon/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            var d = R * c;

            document.getElementById(\"distance_direct\").innerHTML = \"<br/>Distanza tra i due punti (in linea d'aria): \"+d;

            var travelmode = document.getElementById(\"travelMode\").value;

            if (travelmode == \"driving\")
                travel = google.maps.DirectionsTravelMode.DRIVING;
            else if (travelmode == \"walking\")
                travel = google.maps.DirectionsTravelMode.WALKING;
            else if (travelmode == \"bicycling\")
                travel = google.maps.DirectionsTravelMode.BICYCLING;

		var request = {
                    origin:location1,
                    destination:location2,
                    travelMode: travel
		};
		directionsService.route(request, function(response, status){
                    if (status == google.maps.DirectionsStatus.OK){
                        var price = response.routes[0].legs[0].distance.value /1000 * $costokm + 5.0;
                        directionsDisplay.setDirections(response);
                        distance = 'Distanza tra i due punti scelti sul percorso: '+response.routes[0].legs[0].distance.text;
                        distance += '<br/>Tempo approssimativo '+travelmode+': '+response.routes[0].legs[0].duration.text;
                        distance += '<br/>Costo approssimativo in euro: '+price;
                        document.getElementById('distance_road').innerHTML = distance;
                    } else {
                        alert('error: ' + status);
                    }
		});

		var text1 = '<div id=\"content\">'+
				'<h1 id=\"firstHeading\">First location</h1>'+
				'<div id=\"bodyContent\">'+
				'<p>Coordinates: '+location1+'</p>'+
				'<p>Address: '+address1+'</p>'+
				'</div>'+
				'</div>';

		var text2 = '<div id=\"content\">'+
			'<h1 id=\"firstHeading\">Second location</h1>'+
			'<div id=\"bodyContent\">'+
			'<p>Coordinates: '+location2+'</p>'+
			'<p>Address: '+address2+'</p>'+
			'</div>'+
			'</div>';

		infowindow1.setContent(text1);
		infowindow2.setContent(text2);
	}

	function toRad(deg){
            return deg * Math.PI/180;
	}
    ");

$document->addScriptDeclaration("

");

//$pflip = modPflipHelper::myfunction();

require JModuleHelper::getLayoutPath('mod_taxi', $params->get('layout', 'default'));

