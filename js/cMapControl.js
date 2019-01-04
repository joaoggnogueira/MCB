
/* global google */

function cMapControl() {

    this.mapdiv = cUI.catchElement("map");
    this.searchdiv = cUI.catchElement("input-group-search");
    this.inputsearchmun = cUI.catchElement("pac-input");
    this.inputsearchinst = cUI.catchElement("inst-input");
    this.selectsearch = cUI.catchElement("select-search");
    this.closeFilterInstBtn = cUI.catchElement("close-filter-inst-btn");
    this.treeSelectMode = cUI.catchElement("selected-mode");
    this.configVisualBtn = cUI.catchElement("config-visualizacao");
    this.headerGroup = cUI.catchElement("header-group");

    this.inputsearchmun.hide();
    this.inputsearchinst.hide();
    this.treeSelectMode.hide();
    this.selectsearch.hide();

    this.orange_marker = {
        url: ROOT_APP + "images/marker/spotlight-poi-dotless-orange.png",
        size: new google.maps.Size(27, 43),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(13.5, 43),
        labelOrigin: new google.maps.Point(13.5, 15)
    };
    this.blue_marker = {
        url: ROOT_APP + "images/marker/spotlight-poi-dotless-blue.png",
        size: new google.maps.Size(27, 43),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(13.5, 43),
        labelOrigin: new google.maps.Point(13.5, 15)
    };
    this.orange_circle = {
        strokeColor: '#FF1111',
        fillColor: '#FF1111'
    };
    this.blue_circle = {
        strokeColor: "#1111FF",
        fillColor: "#1111FF",
        fillOpacity: 0,
        srokeOpacity: 0
    };

    this.hashMarkers = false;
    this.markerCluster = false;
    this.visualType = 0;
    this.markerType = 0;
    this.instModeId = false;
    this.markerSelected = false;
    this.atualStateMun = false;
    this.mapaInfo = false;

    this.inputsearchinst.hide();

    this.getMarkerType = function () {
        return this.visualType;
    };

    var initialpos = {
        zoom: 4,
        center: {lat: -13.9731974397433545, lng: -51.92527999999999},
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        zoomControl: true,
        scaleControl: true,
        fullscreenControl: false
    };

    this.googlemap = new google.maps.Map(this.mapdiv, initialpos);

    var ctrl = this;
    var initialized = false;

    this.setMapInfo = function (info) {
        console.log(info);
        ctrl.mapInfo = info;
    };

    this.init = function () { //inicializa interface caso não inicializada
        if (!ctrl.initialized) {
            ctrl.inputsearchmun.toggleSlideHorizontal(400);
            ctrl.treeSelectMode.toggleSlideHorizontal(400);
            ctrl.selectsearch.toggleSlideHorizontal(400);
            ctrl.initialized = true;
            $("#splash").remove();
        }
    };

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
    this.appendLogo = function (domelem) {
        if (domelem) {
            ctrl.googlemap.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(domelem);
        } else {
            console.log("Objeto no Append é nulo");
        }
    };

    this.unselectMarker = function () {
        if (ctrl.markerSelected) {
            if (ctrl.markerSelected.setIcon) {
                ctrl.markerSelected.setIcon(ctrl.orange_marker);
            } else {
                var option = JSON.parse(JSON.stringify(ctrl.orange_circle));
                option.fillOpacity = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.opacity.valor / 100.0;
                option.strokeOpacity = 0.8;
                ctrl.markerSelected.setOptions(option);
            }
            ctrl.markerSelected = false;
        }
        ctrl.hideKML();
    };
    this.hideKML = function () {
        ctrl.googlemap.data.setStyle(function (feature) {
            return {visible: false};
        });
    };
    this.showKML = function (id) {

        ctrl.hideKML();

        if (ctrl.markerType === 0) {
            var uf_id = ("" + id).substr(0, 2);
            if (uf_id != ctrl.atualStateMun) {
                if (ctrl.atualStateMun != false) {
                    ctrl.googlemap.data.forEach(function (feature) {
                        ctrl.googlemap.data.remove(feature);
                    });
                }
                ctrl.atualStateMun = uf_id;
                $.getJSON('./shapes/geojs-' + uf_id + '-mun.json', function (data) {
                    ctrl.googlemap.data.addGeoJson(data);
                    ctrl.googlemap.data.setStyle(function (feature) {
                        var geocodigo = feature.getProperty('id');
                        return {
                            fillColor: "#0000FF",
                            strokeWeight: 1.0,
                            fillOpacity: 0.5,
                            strokeColor: "#0000FF",
                            strokeOpacity: 1,
                            visible: (id == geocodigo)
                        };
                    });
                });
            } else {
                ctrl.googlemap.data.setStyle(function (feature) {
                    var geocodigo = feature.getProperty('id');
                    return {
                        fillColor: "#0000FF",
                        strokeWeight: 1.0,
                        fillOpacity: 0.3,
                        strokeColor: "#0000FF",
                        strokeOpacity: 1,
                        visible: (id == geocodigo)
                    };
                });
            }
        } else if (ctrl.markerType === 1) {
            this.googlemap.data.setStyle(function (feature) {
                var geocodigo = feature.getProperty('geocodigo');
                return {
                    fillColor: "#0000FF",
                    strokeWeight: 1.5,
                    fillOpacity: 0.4,
                    strokeColor: "#0000FF",
                    strokeOpacity: 1,
                    visible: (id == geocodigo)
                };
            });
        } else if (ctrl.markerType === 2) {
            ctrl.googlemap.data.forEach(function (feature) {
                ctrl.googlemap.data.remove(feature);
            });
            $.getJSON('./shapes/regiao/' + id + '.json', function (data) {
                ctrl.googlemap.data.addGeoJson(data);
                ctrl.googlemap.data.setStyle(function (feature) {
                    return {
                        fillColor: "#0000FF",
                        strokeWeight: 2.0,
                        fillOpacity: 0.5,
                        strokeColor: "#0000FF",
                        strokeOpacity: 1,
                        visible: true
                    };
                });
            });
        }

    };

    this.changeMarkerType = function (ind) {
        ctrl.oldMarkerType = ctrl.markerType;
        ctrl.markerType = ind;
        if (ctrl.visualType === 0 && (ctrl.markerType === 1 || ctrl.markerType === 2)) {
            ctrl.visualType = 1;
            window.cUserConfig.close_dialog();
        } else if ((ctrl.visualType === 1 && (ctrl.markerType === 0))) {
            ctrl.visualType = 0;
            window.cUserConfig.close_dialog();
        }

        ctrl.googlemap.data.forEach(function (feature) {
            ctrl.googlemap.data.remove(feature);
        });

        if (ctrl.markerType === 1) {
            $.getJSON('./shapes/estado/all.geojson', function (data) {
                ctrl.googlemap.data.addGeoJson(data);
                ctrl.googlemap.data.setStyle(function (feature) {
                    return {visible: false};
                });
            });
        }
        ctrl.atualStateMun = false;

        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
        ctrl.hideKML();
    };

    this.changeVisualType = function (ind) {
        window.cUserConfig.close_dialog();
        ctrl.visualType = ind;
        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
    };

    this.updateVisualType = function () {
        if (ctrl.visualType === 0) {

        } else if (ctrl.visualType === 1) {

        } else if (ctrl.visualType === 2) {
            var fator = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.fator.valor;
            var min = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.min.valor;
            var opacity = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.opacity.valor;

            for (var key in ctrl.hashMarkers) {
                var marker = ctrl.hashMarkers[key];
                var radius = parseInt(marker.label.text) * fator + min;
                marker.setRadius(radius);
                marker.setOptions({fillOpacity: opacity / 100.0});
            }
        }
    };

    this.requestUpdate = function (filters) {
        if (ctrl.instModeId === false) {
            filters.instituicao = {
                all: true
            };
        } else {
            filters.instituicao = [ctrl.instModeId];
        }
        filters.markerType = ctrl.markerType;
        cUI.filterCtrl.disableFilters();

        $("#visual-selected-text").val(ctrl.visualType);
        $("#marker-selected-text").val(ctrl.markerType);
        $("#visual-selected-text").selectmenu("refresh");
        $("#marker-selected-text").selectmenu("refresh");
        var timer;

        function timer_function() {
            if (typeof MarkerClusterer !== "undefined") {
                clearInterval(timer);
                var mapa = ctrl.mapInfo.id;
                cData.requestMarkers(filters, mapa, ctrl.setData);
            } else {
                console.log("Aguardando MarkerClusterer ser carregado");
            }
        }

        timer = setInterval(timer_function, 1000);
    };

    this.loadData = function (id) {
        cData.getConfiguracoes(id, function (datare) {
            var data = JSON.parse(datare.json);
            cUI.filterCtrl.setFilters(data.filter);
            cUI.mapCtrl.changeVisualType(data.markertype);
        });
    };

    this.onchangeSelectSearch = function () {

        ctrl.inputsearchmun.slideUp(400);
        ctrl.inputsearchinst.slideUp(400);

        switch (ctrl.selectsearch.value) {
            case "municipio":
                ctrl.inputsearchmun.slideDown(400);
                break;
            case "instituicao":
                ctrl.inputsearchinst.slideDown(400);
                break;
        }
        setTimeout(function () {
            switch (ctrl.selectsearch.value) {
                case "municipio":
                    ctrl.inputsearchmun.focus();
                    break;
                case "instituicao":
                    ctrl.inputsearchinst.focus();
                    break;
            }
        }, 500);
    };

    this.buscar = function () {
        var place = ctrl.autocomplete.getPlace();
        if (!place.geometry) {
            $.ajax({
                url: "http://maps.google.com/maps/api/geocode/json?address=" + ctrl.inputsearchmun.value + "&types=(cities)&components=country:BR",
                type: 'POST',
                dataType: 'json',
                timeout: 20000,
                success: function (data) {
                    if (data.results.length !== 0) {
                        place = data.results[0];
                        ctrl.inputsearchmun.value = place.formatted_address;
                        ctrl.googlemap.setCenter(place.geometry.location);
                        ctrl.googlemap.setZoom(10);
                    } else {
                        window.alert("Nenhum município encontrado com" + ctrl.inputsearchmun.value);
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
    };

    this.enableZoomCluster = function () {
        setTimeout(function () {
            if (ctrl.markerCluster) {
                ctrl.markerCluster.zoomOnClick_ = true;
            }
        }, 50);
    };

    this.disableZoomCluster = function () {
        if (ctrl.markerCluster) {
            ctrl.markerCluster.zoomOnClick_ = false;
        }
    };

    this.removeAllMarkers = function () {
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
    };

    var percentColors = [
        {pct: 0.0, color: {r: 0xff, g: 0xff, b: 0xff}},
        {pct: 1.0, color: {r: 0x00, g: 0x00, b: 0xff}}
    ];

    function map_recenter(latlng, offsetx, offsety) {
        var scale = Math.pow(2, ctrl.googlemap.getZoom());

        var worldCoordinateCenter = ctrl.googlemap.getProjection().fromLatLngToPoint(latlng);
        var pixelOffset = new google.maps.Point((offsetx / scale) || 0, (offsety / scale) || 0);

        var worldCoordinateNewCenter = new google.maps.Point(
                worldCoordinateCenter.x - pixelOffset.x,
                worldCoordinateCenter.y + pixelOffset.y
                );

        var newCenter = ctrl.googlemap.getProjection().fromPointToLatLng(worldCoordinateNewCenter);

        ctrl.googlemap.panTo(newCenter);
    }

    var getColorForPercentage = function (pct) {
        pct = Math.pow(pct, 1 / 5);
        for (var i = 1; i < percentColors.length - 1; i++) {
            if (pct < percentColors[i].pct) {
                break;
            }
        }
        var lower = percentColors[i - 1];
        var upper = percentColors[i];
        var range = upper.pct - lower.pct;
        var rangePct = (pct - lower.pct) / range;
        var pctLower = 1 - rangePct;
        var pctUpper = rangePct;
        var color = {
            r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
            g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
            b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
        };
        return 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
        // or output as hex if preferred
    };

    this.eventOpenMapMarkerControl = function (marker, cod_mun, name_mun, uf_mun) {
        var position;
        if (marker.getPosition) {
            position = marker.getPosition();
        } else {
            position = marker.getCenter();
        }
        map_recenter(position, -(($(window).width() - 100) / 4), 0);
        ctrl.unselectMarker();
        ctrl.showKML(cod_mun);
        if (marker.setIcon) {
            marker.setIcon(ctrl.blue_marker);
        } else {
            marker.setOptions(ctrl.blue_circle);
        }

        ctrl.markerSelected = marker;
        var filters = cUI.filterCtrl.getFilters();
        if (ctrl.instModeId === false) {
            filters.instituicao = {all: true};
        } else {
            filters.instituicao = [ctrl.instModeId];
        }
        filters.markerType = ctrl.markerType;
        cData.listCursos(cod_mun, ctrl.mapInfo.id, filters, function (data) {
            cUI.filterCtrl.close();
            cUI.markerDialogCtrl.open({uf: uf_mun, cod_mun: cod_mun, name_mun: name_mun, data: data, mapInfo: ctrl.mapInfo});
        });
    };

    this.setData = function (data) {
        
        if(ctrl.instModeId != false && data.length == 0){
            swal({html:"Nenhum resultado foi encontrado"});
            ctrl.DesabilitarModoInstituicao();
            return;
        }
        
        ctrl.removeAllMarkers();

        const fator = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.fator.valor;
        const min = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.min.valor;
        const opacity = cUserConfig.data[ctrl.mapInfo.id][2].cs[ctrl.markerType].vs.opacity.valor / 100.0;
        var bounds = new google.maps.LatLngBounds();

        var count = -1;
        if (ctrl.visualType === 3) {
            var hashState = [];
            var hash = [];
            var hashMun = [];
            var max = 0;
            var bounds = new google.maps.LatLngBounds();
            var total = 0;
            data.map(function (mun) {
                var uf_id = ("" + mun[3]).substr(0, 2);
                if (!hashState[uf_id]) {
                    hashState[uf_id] = uf_id;
                    total++;
                }
                if (max < parseInt(mun[0])) {
                    max = parseInt(mun[0]);
                }

                var lat = parseFloat(mun[2]);
                var lng = parseFloat(mun[1]);

                bounds.extend({lng: lng, lat: lat});
                hash.push(mun[3] + "");
                hashMun[mun[3]] = mun;
                count++;

            });

            var ready = 0;
            console.log(count);
            hashState.map(function (uf_id) {
                $.getJSON('./shapes/geojs-' + uf_id + '-mun.json', function (data) {
                    ctrl.googlemap.data.addGeoJson(data);
                    ready++;
                    if (ready === total) {
                        ctrl.googlemap.data.setStyle(function (feature) {
                            var geocodigo = "" + feature.getProperty('id');
                            var value = hashMun[geocodigo];
                            if (hash.includes(geocodigo)) {
                                return {
                                    fillColor: getColorForPercentage(value[0] / max),
                                    strokeWeight: 1.5,
                                    fillOpacity: 0.75,
                                    strokeColor: getColorForPercentage(value[0] / max),
                                    strokeOpacity: 1,
                                    visible: true
                                };
                            } else {
                                return {visible: false};
                            }
                        });
                    }
                });
            });
        } else {
            var markers = data.map(function (mun) {
                count++;
                var marker;
                var lat = parseFloat(mun[2]);
                var lng = parseFloat(mun[1]);

                bounds.extend({lng: lng, lat: lat});
                if (ctrl.visualType === 2) {
                    marker = new google.maps.Circle({
                        strokeColor: ctrl.orange_circle.strokeColor,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: ctrl.orange_circle.fillColor,
                        fillOpacity: opacity,
                        label: {text: mun[0]},
                        map: ctrl.googlemap,
                        center: {lng: lng, lat: lat},
                        radius: mun[0] * fator + min,
                        zIndex: count
                    });
                } else {
                    var marker_data = {
                        position: {lng: lng, lat: lat},
                        icon: ctrl.orange_marker,
                        label: {
                            text: mun[0],
                            color: 'white',
                            fontSize: '12px',
                            x: '30',
                            y: '10'
                        }
                    };
                    if (ctrl.visualType === 1) {
                        marker_data.map = ctrl.googlemap;
                    }

                    marker = new google.maps.Marker(marker_data);
                }
                ctrl.hashMarkers[mun[3]] = marker;
                const cod_mun = mun[3];
                const name_mun = mun[4];
                const uf_mun = mun[5];
                marker.addListener('click', function (data) {
                    ctrl.eventOpenMapMarkerControl(marker, cod_mun, name_mun, uf_mun);
                });
                return marker;
            });
        }

        ctrl.googlemap.fitBounds(bounds);
        
        if (ctrl.googlemap.getZoom() >= 10) {
            ctrl.googlemap.setZoom(10);
        }

        if (typeof (google) !== "undefined" && ctrl.visualType === 0) {
            ctrl.markerCluster = new MarkerClusterer(ctrl.googlemap, markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        }
        cUI.filterCtrl.enableFilters();

        ctrl.init();
    };

    this.HabiliarModoInstituicao = function (id_inst, sigla_inst, nome_inst) {
        ctrl.visualType = 1;
        ctrl.markerType = 0;
        cUI.filterCtrl.fadeOut(400);
        cUI.filterCtrl.resetFilters(false);
        ctrl.instModeId = id_inst;
        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
        ctrl.closeFilterInstBtn.child(".pac-addon-header").html(sigla_inst);
        ctrl.closeFilterInstBtn.child(".pac-addon-content").html(nome_inst);
        ctrl.closeFilterInstBtn.show();
        ctrl.inputsearchinst.hide();
        ctrl.selectsearch.hide();
    };

    this.DesabilitarModoInstituicao = function () {
        if (ctrl.instModeId !== false) {
            if (ctrl.visualType === 1) {
                ctrl.visualType = 0;
            }
            cUI.filterCtrl.fadeOut(400);
            ctrl.instModeId = false;
            ctrl.requestUpdate(cUI.filterCtrl.getFilters());
            ctrl.closeFilterInstBtn.hide();
            ctrl.inputsearchinst.show();
            ctrl.selectsearch.show();
            ctrl.inputsearchinst.value = "";
            cUI.markerDialogCtrl.close();
        }
    };

    this.ShowAtualVisualConfigDialog = function (event, data) {
        window.cUserConfig.config_dialog(ctrl.mapInfo.id, ctrl.visualType, ctrl.updateVisualType, ctrl.markerType);
    };

    this.ShowVisualConfigDialog = function (event, data) {
        window.cUserConfig.config_dialog(ctrl.mapInfo.id, data.ind, ctrl.updateVisualType, ctrl.markerType);
    };

    this.backToHome = function () {
        swal({
            text: "Deseja sair?",
            showCancelButton: true,
            confirmButtonColor: '#d03536',
            cancelButtonColor: '#777777',
            confirmButtonText: 'SAIR',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                window.location = "./index.html";
            }
        });
    };

    this.closeFilterInstBtn.hide();
    this.selectsearch.change(this.onchangeSelectSearch);
    this.appendLeft(this.searchdiv);
    this.appendLeft(this.treeSelectMode);
    this.appendLeft(this.headerGroup);
    this.headerGroup.child(".button-home").click(this.backToHome);

    this.appendLogo(cUI.catchElement("logotipo_unesp"));
    this.appendLogo(cUI.catchElement("logotipo_sbc"));
    var options = {
        componentRestrictions: {country: 'br'}
    };
    var autocomplete = new google.maps.places.Autocomplete(this.inputsearchmun, options);
    autocomplete.bindTo('bounds', this.googlemap);

    ctrl.autocomplete = autocomplete;

    autocomplete.addListener('place_changed', ctrl.buscar);

    this.closeFilterInstBtn.click(this.DesabilitarModoInstituicao);
    this.configVisualBtn.click(this.ShowAtualVisualConfigDialog);

    $("#visual-selected-text").selectmenu({
        change: function (event, ui) {
            var index = parseInt(ui.item.value);
            if (index === 1 && ctrl.markerType === 0) {
                swal({
                    title: 'Alerta',
                    text: "Marcadores sem agrupamentos podem levar um tempo maior para carregar! Deseja continuar?",
                    showCancelButton: true,
                    confirmButtonColor: '#777',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        ctrl.changeVisualType(index);
                    } else {
                        $("#visual-selected-text").val(ctrl.visualType).selectmenu("refresh");
                    }
                });
            } else {
                ctrl.changeVisualType(index);
            }
        }
    });
    $("#marker-selected-text").selectmenu({
        change: function (event, ui) {
            var index = parseInt(ui.item.value);
            ctrl.changeMarkerType(index);
        }
    });
    cData.listInstituicoes(function (list) {

        for (var i = 0; i < list.length; i++) {
            if (list[i].sigla.length > 2) {
                list[i].value = list[i].sigla;
            } else {
                list[i].value = list[i].nome;
                list[i].sigla = list[i].nome;
            }
        }

        $("#inst-input").autocomplete({
            minLength: 3,
            source: list,
            focus: function (event, ui) {
                return false;
            },
            select: function (event, ui) {
                $("#inst-input").val(ui.item.sigla);
                ctrl.HabiliarModoInstituicao(ui.item.id, ui.item.sigla, ui.item.nome);
                return false;
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>").append("<div><b>" + item.sigla + "</b><br>" + item.nome + "</div>").appendTo(ul);
        };
    });

    google.maps.event.addListener(this.googlemap, 'dragstart', this.disableZoomCluster);
    google.maps.event.addListener(this.googlemap, 'mouseup', this.enableZoomCluster);
}
