
var Map = function () {


    var input = document.getElementById('pac-input');

    var map;
    var latlng;
    var geocoder;
    var marker;
    var infoWindow;

    //area of polygons
    var area = [];
    //cost of polygons
    var cost = [];
    //array of coordinates of maps
    var coordinates = [];
    //counters
    var i = 0;
    var c = 0;
    var j = 0;
    ///end

    //markers control
    var markerCount = 0;
    var markersArray = [];
    //end

    //array of polygons check if closed
    var isClosed = [];
    //poly line
    var poly;
    var path;
    //

    //declare of polygon single
    var polygon;

    //declare array of polygons
    var polygons = [];
    var polygonsCordinates = [];
    // centers of polygons
    var centerLat;
    var centerLng;
    //end

    //global app cost for meter
    var applicationCost = 0;

    var coordinatesJson;

    //display
    var points = [];

    //

    var init = function () {
        //initMap(true, true);

        //google.maps.event.addDomListener(window, 'load', initMap);
    };

    var initMap = function (clickable, searchable, draggable, drawable) {


        var lat = $('#lat').val();
        var lng = $('#lng').val();
        latlng = new google.maps.LatLng('24.7136', '46.6753');
        if (!lat && !lng || lat == 0 && lng ==0) {
                    if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
               latlng = new google.maps.LatLng(latitude, longitude);

           });
       } else {
       latlng = new google.maps.LatLng('24.7136', '46.6753');
       }
        } else {
            //alert('here');
            latlng = new google.maps.LatLng(lat, lng);
        }


        var mapOptions = {
            center: latlng,
            zoom: 18,
            draggable: true
        }
        if (!draggable) {
            mapOptions.draggable = false
        }
        map = new google.maps.Map(document.getElementById("map"), mapOptions);

        if (!drawable)
        {
            geocode(map, latlng);
        }


        if (clickable) {

            mapOnClick();
        }
        //markerOnClick();
        if (searchable) {
            search(drawable);
        }


        if (drawable)
        {
            isClosed[i] = false;
            drawOnePolygon();
        }


    }

    var drawOnePolygon = function () {

    //console.log(points);
        drawPoly(map);
        google.maps.event.addListener(map, 'click', function (clickEvent) {
            if (polygons.length == 1) {
                return;
            }
            if (isClosed[i]) {
                return;
            }

            var markerIndex = poly.getPath().length;
            var isFirstMarker = markerIndex === 0;

            createMarker(map, clickEvent.latLng, true, null);

            markersArray[markerCount] = marker;
            markerCount++;
            if (isFirstMarker) {
                google.maps.event.addListener(marker, 'click', function () {

                    if (isClosed[i]) {
                        return;
                    } else {
                        if (poly.getPath().length > 3) {
                            //alert('here');
                            // get area of poly
                            area[j] = google.maps.geometry.spherical.computeArea(poly.getPath());
                            //alert(area[j]);
                            area[j] = area[j].toFixed(0);
                            //end


                            //get paths of pollllly
                            polygonPoints(poly);
                            //LatLngs[j] = JSON.stringify(LatLngs[j]);

                            //get center of polygon
                            polygonCenter(poly);
                            //get center
                            ///end
                        } else {
                            alert("من فضلك حدد 4 اتجاهات ك حد ادني");
                            return;
                        }
                    }
                    //alert('here');
                    path = poly.getPath();
                    poly.setMap(null);
                    polygon = drawPolygon(map, path);

                    polygons[i] = polygon;
                    polygonsCordinates[i] = coordinates[j];
                    isClosed[i] = true;
                    i++;
                    j++;
                    isClosed[i] = false;

                    drawPoly(map);

                });
            }
            google.maps.event.addListener(marker, 'drag', function (dragEvent) {


                //console.log(markerIndex);
                polygon.getPath().setAt(markerIndex, dragEvent.latLng);

                j--;
                // get area of poly
                area[j] = google.maps.geometry.spherical.computeArea(polygon.getPath());
                area[j] = area[j].toFixed(0);
                //end

                //get paths of pollllly

                polygonPoints(poly);


                //get center of polygon
                polygonCenter(polygon);
                //get center

                j++;
                ///end


            });
            poly.getPath().push(clickEvent.latLng);

        });
    }
    var drawPolygons = function () {


        drawPoly(map);
        google.maps.event.addListener(map, 'click', function (clickEvent) {
            if (isClosed[i]) {
                return;
            }

            var markerIndex = poly.getPath().length;
            //alert(markerIndex);
            var isFirstMarker = markerIndex === 0;

            createMarker(map, clickEvent.latLng, true, null);

            markersArray[markerCount] = marker;
            markerCount++;
            if (isFirstMarker) {
                google.maps.event.addListener(marker, 'click', function () {
                    if (isClosed[i]) {
                        return;
                    } else {
                        if (poly.getPath().length > 3) {
                            //alert('here');
                            // get area of poly
                            area[j] = google.maps.geometry.spherical.computeArea(poly.getPath());
                            //alert(area[j]);
                            area[j] = area[j].toFixed(0);
                            //end

                            //calclaute of cost
                            cost[j] = area[j] * applicationCost;
                            cost[j] = cost[j].toFixed(0);
                            //end

                            //get paths of pollllly
                            coordinates[j] = polygonPoints(poly);
                            //LatLngs[j] = JSON.stringify(LatLngs[j]);

                            //get center of polygon
                            polygonCenter(poly);
                            //get center
                            ///end
                        } else {
                            alert("من فضلك حدد 4 اتجاهات ك حد ادني");
                            return;
                        }
                    }
                    //alert('here');
                    path = poly.getPath();
                    poly.setMap(null);
                    polygon = drawPolygon(map, path);

                    polygons[i] = polygon;
                    polygonsCordinates[i] = coordinates[j];
                    isClosed[i] = true;
                    i++;
                    j++;
                    isClosed[i] = false;

                    drawPoly(map);

                });
            }
            google.maps.event.addListener(marker, 'drag', function (dragEvent) {


                //console.log(markerIndex);
                polygon.getPath().setAt(markerIndex, dragEvent.latLng);

                j--;
                // get area of poly
                area[j] = google.maps.geometry.spherical.computeArea(polygon.getPath());
                area[j] = area[j].toFixed(0);
                //end

                //calclaute of cost
                cost[j] = area[j] * applicationCost;
                cost[j] = cost[j].toFixed(0);
                //end


                //get paths of pollllly
                coordinates[j] = polygonPoints(polygon);
                //LatLngs[j] = JSON.stringify(LatLngs[j]);

                //get center of polygon
                polygonCenter(polygon);
                //get center

                j++;
                ///end



                // $("#area").html("<div><p class=\"grob\"><i class=\"fa fa-crop\" aria-hidden=\"true\"></i> المساحة: <span> <span> " + area + "  (م <em class=\"matrs\">2</em> ) </span></span></p></div>\n");
                // $("#cost").html("<p class=\"grob\"><i class=\"fa fa-money\" aria-hidden=\"true\"></i> السعر: <span>" + cost + " SR </span></p>");

            });
            poly.getPath().push(clickEvent.latLng);


        });
    }


    var checkPolygon = function (latLng) {
        for (k = 0; k < polygons.length; k++)
        {
            //console.log(polygons[k]);
            //return;
            //var isWithinPolygon = polygons[k].containsLatLng(latLng);
            // console.log(isWithinPolygon);
            // if(isWithinPolygon)
            // {
            //     return k;
            // }
            //console.log(isWithinPolygon);

            //console.log(polygonsCordinates[k]);
            checker = polygonPointsChecker(polygons[k], latLng);
            if (checker == true)
            {
                return k;
            } else
            {
                continue;
            }

        }
        return null;

    }

    var drawpolygonsOld = function (locations) {
        var counter = 0;
        jQuery(document).ready(function () {
            for (i = 0; i < locations.length; i++) {
                points[i] = [];
                for (j = 0; j < locations[i].length; j++) {
                    var point = locations[i][j].split(",");
                    var pointOb = {};
                    pointOb.lat = parseFloat(point[0]);
                    pointOb.lng = parseFloat(point[1]);
                    points[i][j] = pointOb;
                    counter++;
                }
                //Construct the polygon.
                polygon = drawPolygon(map, points[i]);
                polygons[i] = polygon;
            }
        })
    }
    var drawpolygonOld = function (location) {
        //console.log(location);
    
        for (j = 0; j < location.length; j++) {
            var pointOb = {};
            pointOb.lat = parseFloat(location[j].lat);
            pointOb.lng = parseFloat(location[j].lng);
            points[j] = pointOb;
        }
        polygon = drawPolygon(map, points);
        //console.log(polygon);
        polygons[i] = polygon;
    }
    var drawPoly = function (map) {

        poly = new google.maps.Polyline({
            map: map,
            path: [],
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2
        });
    }

    var drawPolygon = function (map, path) {
        return new google.maps.Polygon({
            map: map,
            path: path,
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#FF0000",
            fillOpacity: 0.35
        });
    }


    var polygonPoints = function (polygon) {
        var len = polygon.getPath().getLength();
        for (var i = 0; i < len; i++) {
            var lat = polygon.getPath().getAt(i).lat();
            var lng = polygon.getPath().getAt(i).lng();
            //point = polygon.getPath().getAt(i).lat() + "," + polygon.getPath().getAt(i).lng();
            coordinates.push({"lat": lat, "lng": lng});
        }

    }
    var polygonPoints2 = function (polygon) {
        var len = polygon.getPath().getLength();
        //var point;
        var arrayLatLng = [];
        for (var i = 0; i < len; i++) {
            var lat = polygon.getPath().getAt(i).lat();
            var lng = polygon.getPath().getAt(i).lng();
            //point = polygon.getPath().getAt(i).lat() + "," + polygon.getPath().getAt(i).lng();
            arrayLatLng.push({"lat": lat, "lng": lng});
        }
        //console.log(arrayLatLng);
        return arrayLatLng;

    }
    var polygonPointsChecker = function (polygon, point) {
        var len = polygon.getPath().getLength();
        var htmlStr = "";
        var current;
        for (var i = 0; i < len; i++) {

            current = polygon.getPath().getAt(i).lat() + "," + polygon.getPath().getAt(i).lng();
            if (current === point)
            {
                return true;
            }
        }

        return false;

    }

    var polygonCenter = function (poly) {

        var lowx,
                highx,
                lowy,
                highy,
                lats = [],
                lngs = [],
                vertices = poly.getPath();

        for (var i = 0; i < vertices.length; i++) {
            lngs.push(vertices.getAt(i).lng());
            lats.push(vertices.getAt(i).lat());
        }

        lats.sort();
        lngs.sort();
        lowx = lats[0];
        highx = lats[vertices.length - 1];
        lowy = lngs[0];
        highy = lngs[vertices.length - 1];
        center_x = lowx + ((highx - lowx) / 2);
        center_y = lowy + ((highy - lowy) / 2);

        centerLat = center_x;
        centerLng = center_y;

    }
    var geocode = function (map, latlng) {
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': latlng}, function (results, status) {
            console.log(status);
            if (status === 'OK') {
                if (results[0]) {
                    map.setZoom(15);
                    console.log(results[0].formatted_address);
                    createMarker(map, latlng,false,results[0].formatted_address);
                    document.getElementById('lat').value = latlng.lat();
                    document.getElementById('lng').value = latlng.lng();
                    console.log(results[0]);
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    }

    var createMarker = function (map, latlng, drag, content) {
        var markerOptions = {
            position: latlng,
            map: map
        }

        if (drag)
        {
            markerOptions.draggable = true;
            markerOptions.index = markerCount;
        }
        marker = new google.maps.Marker(markerOptions);
        if (content !== null)
        {
            addInfoWindow(marker, content, latlng);
        }

        return marker;
    }


    var mapOnClick = function () {
        google.maps.event.addListener(map, 'click', function (event) {
            marker.setMap(null);
            geocode(map, event.latLng);

        });
    }
    var markerOnClick = function () {
        google.maps.event.addListener(marker, 'click', function () {
            infoWindow.open(map);
        });
    }
    var addInfoWindow = function (marker, content, latlng) {
        var infoWindowOptions = {
            content: content,
            position: latlng
        }
        infoWindow = new google.maps.InfoWindow(infoWindowOptions);
        infoWindow.open(map, marker);
    }

    var mapAutocomplete = function () {
        var input = document.getElementById('pac-input');

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        //var infowindowContent = document.getElementById('infowindow-content');
        //infoWindow.setContent(infowindowContent);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('lng').value = place.geometry.location.lng();
            if (!place.place_id) {
                return;
            }
            geocoder.geocode({'placeId': place.place_id}, function (results, status) {

                if (status !== 'OK') {
                    window.alert('Geocoder failed due to: ' + status);
                    return;
                }
                map.setZoom(11);
                map.setCenter(results[0].geometry.location);
                // Set the position of the marker using the place ID and location.
                marker.setPlace({
                    placeId: place.place_id,
                    location: results[0].geometry.location
                });
                marker.setVisible(true);
                var infowindowContent = document.getElementById('infowindow-content');
                infowindowContent.children['place-name'].textContent = place.name;
                infowindowContent.children['place-id'].textContent = place.place_id;
                infowindowContent.children['place-address'].textContent =
                        results[0].formatted_address;
                //infoWindow.open(map, marker);
            });
        });

    }

    var search = function (dragable) {

        //var input = document.getElementById('pac-input');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        //var infowindow = new google.maps.InfoWindow();
//        var marker = new google.maps.Marker({
//            map: map,
//            anchorPoint: new google.maps.Point(0, -29)
//        });

        autocomplete.addListener('place_changed', function () {
            //resetMap();
            if (infoWindow !== undefined)
            {
                infoWindow.close();
            }
            if (marker !== undefined)
            {
                marker.setVisible(false);
            }

            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
//            marker.setIcon(({
//                url: place.icon,
//                size: new google.maps.Size(71, 71),
//                origin: new google.maps.Point(0, 0),
//                anchor: new google.maps.Point(17, 34),
//                scaledSize: new google.maps.Size(35, 35)
//            }));
//            marker.setPosition(place.geometry.location);
//            marker.setVisible(true);

//            var address = '';
//            if (place.address_components) {
//                address = [
//                    (place.address_components[0] && place.address_components[0].short_name || ''),
//                    (place.address_components[1] && place.address_components[1].short_name || ''),
//                    (place.address_components[2] && place.address_components[2].short_name || '')
//                ].join(' ');
//            }

            if (!dragable)
            {
                createMarker(map, place.geometry.location,false,place.formatted_address);
            }



            //Location details
//            for (var i = 0; i < place.address_components.length; i++) {
//                if (place.address_components[i].types[0] == 'postal_code') {
//                    document.getElementById('postal_code').innerHTML = place.address_components[i].long_name;
//                }
//                if (place.address_components[i].types[0] == 'country') {
//                    document.getElementById('country').innerHTML = place.address_components[i].long_name;
//                }
//            }
//            document.getElementById('location').innerHTML = place.formatted_address;
            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('lng').value = place.geometry.location.lng();
        });

    }



    var resetMap = function () {
        area = [];
        cost = [];
        coordinates = [];
        i = 0;
        c = 0;
        j = 0;
        markerCount = 0;
        isClosed = [];
        polygons = [];
        Map.initMap(false, true, true, true);

    }
    return {
        init: function () {
            init();
        },
        initMap: function (clickable, searchable, draggable, drawable) {
            initMap(clickable, searchable, draggable, drawable);
            search(false);
        },
        calculateLatLngs: function () {
            return JSON.stringify(coordinates);
        },
        getAreaCoordinates: function () {
            return JSON.stringify(coordinates);
        },
        drawPolygonOld: function (locations) {
            coordinates = locations;
            return drawpolygonOld(locations);
        },
        drawPolygonOld2: function (locations) {
            coordinates = locations;
            return drawpolygonsOld(locations);
        },
        resetMap: function () {
            return resetMap();
        },

    };

}();
jQuery(document).ready(function () {
    Map.init();
});

