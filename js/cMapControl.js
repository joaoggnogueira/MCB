
/* global google */

function cMapControl() {

    this.mapdiv = cUI.catchElement("map");
    this.searchdiv = cUI.catchElement("input-group-search");
    this.inputsearch = cUI.catchElement("pac-input");
    this.hashMarkers = false;
    this.markerCluster = false;
    this.markerType = 0;

    this.getMarkerType = function () {
        return this.markerType;
    };

    var initialpos = {
        zoom: 4,
        center: {lat: -13.9731974397433545, lng: -51.92527999999999},
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        zoomControl: true,
        scaleControl: true,
        fullscreenControl: true
    };

    this.googlemap = new google.maps.Map(this.mapdiv, initialpos);

    var ctrl = this;

    this.appendLeft = function (domelem) {
        if (domelem) {
            ctrl.googlemap.controls[google.maps.ControlPosition.TOP_LEFT].push(domelem);
        } else {
            console.log("Objeto no Append é nulo");
        }
    };
    this.appendRight = function (domelem) {
        if (domelem) {
            ctrl.googlemap.controls[google.maps.ControlPosition.TOP_RIGHT].push(domelem);
        } else {
            console.log("Objeto no Append é nulo");
        }
    };

    this.changeMarkerType = function (ind) {
        ctrl.markerType = ind;
        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
    };

    this.requestUpdate = function (filters) {
        console.log(filters);
        cUI.filterCtrl.disableFilters();
        cData.requestMarkers(filters, ctrl.setData);
    };

    this.loadData = function (id) {
        cData.getConfiguracoes(id, function (datare) {
            var data = JSON.parse(datare.json);
            cUI.filterCtrl.setFilters(data.filter);
            cUI.mapCtrl.changeMarkerType(data.markertype);
        });
    };

    this.setData = function (data) {

        if (ctrl.markerCluster) {
            ctrl.markerCluster.clearMarkers();
            ctrl.markerCluster = null;
            ctrl.hashMarkers.length = 0;
        }

        if (ctrl.hashMarkers) {
            for (var key in ctrl.hashMarkers) {
                if (ctrl.hashMarkers[key].setMap) {
                    ctrl.hashMarkers[key].setMap(null);
                }
            }
            ctrl.hashMarkers.length = 0;
        } else {
            ctrl.hashMarkers = [];
        }

        var markers = data.map(function (mun) {
            if (typeof (google) !== "undefined") {
                var marker;
                if (ctrl.markerType === 2) {
                    marker = new google.maps.Circle({
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.35,
                        label: {
                            text: mun[0]
                        },
                        map: ctrl.googlemap,
                        center: {lng: parseFloat(mun[1]), lat: parseFloat(mun[2])},
                        radius: mun[0] * 100 + 10000
                    });
                } else {
                    var marker_data = {
                        position: {lng: parseFloat(mun[1]), lat: parseFloat(mun[2])},
                        label: {
                            text: mun[0],
                            color: 'white',
                            fontSize: '12px',
                            x: '20',
                            y: '70'
                        }
                    };
                    if (ctrl.markerType === 1) {
                        marker_data.map = ctrl.googlemap;
                    }

                    marker = new google.maps.Marker(marker_data);
                }

                ctrl.hashMarkers[mun[3]] = marker;
                const cod_mun = mun[3];
                const name_mun = mun[4];
                const uf_mun = mun[5];
                marker.addListener('click', function () {
                    cData.listCursos(cod_mun, cUI.filterCtrl.getFilters(), function (data) {
                        cUI.filterCtrl.close();
                        cUI.markerDialogCtrl.open({uf: uf_mun,cod_mun: cod_mun, name_mun: name_mun, data: data});
                    });
                });
            }
            return marker;
        });
        if (typeof (google) !== "undefined" && ctrl.markerType === 0) {
            ctrl.markerCluster = new MarkerClusterer(ctrl.googlemap, markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        }
        cUI.filterCtrl.enableFilters();
    };

    this.appendLeft(this.searchdiv);
    var options = {
        types: ['(cities)'],
        componentRestrictions: {country: 'br'}
    };
    var autocomplete = new google.maps.places.Autocomplete(this.inputsearch, options);
    autocomplete.bindTo('bounds', this.googlemap);

    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            $.ajax({
                url: "http://maps.google.com/maps/api/geocode/json?address=" + ctrl.inputsearch.value + "&types=(cities)&components=country:BR",
                type: 'POST',
                dataType: 'json',
                timeout: 20000,
                success: function (data) {
                    if (data.results.length !== 0) {
                        place = data.results[0];
                        ctrl.inputsearch.value = place.formatted_address;
                        ctrl.googlemap.setCenter(place.geometry.location);
                        ctrl.googlemap.setZoom(10);
                    } else {
                        window.alert("Nenhum município encontrado com" + ctrl.inputsearch.value);
                    }
                },
                error: function (data) {
                    window.alert("Falha na busca");
                    console.log(data);
                }
            });
            return;
        }

        if (place.geometry.viewport) {
            ctrl.googlemap.fitBounds(place.geometry.viewport);
        } else {
            ctrl.googlemap.setCenter(place.geometry.location);
            ctrl.googlemap.setZoom(10);
        }
    });


}