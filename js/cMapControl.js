
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
        fillColor: "#1111FF"
    };

    this.hashMarkers = false;
    this.markerCluster = false;
    this.visualType = 0;
    this.markerType = 0;
    this.instModeId = false;
    this.KMLAtual = false;
    this.markerSelected = false;

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
                ctrl.markerSelected.setOptions(ctrl.orange_circle);
            }
            ctrl.markerSelected = false;
        }
        ctrl.hideKML();
    };
    this.hideKML = function(){
        if (ctrl.KMLAtual && ctrl.KMLAtual.setMap) {
            ctrl.KMLAtual.setMap(null);
            ctrl.KMLAtual = false;
        }
    };
    this.showKML = function (id) {
        var kmlArray = false;
        var prefix = false;
        ctrl.hideKML();
        if (ctrl.markerType === 0) {
            return;
        } else if (ctrl.markerType === 1) {
            kmlArray = {
                "11": "RO",
                "12": "AC",
                "13": "AM",
                "14": "RR",
                "15": "PA",
                "16": "AP",
                "17": "TO",
                "21": "MA",
                "22": "PI",
                "23": "CE",
                "24": "RN",
                "25": "PB",
                "26": "PE",
                "27": "AL",
                "28": "SE",
                "29": "BA",
                "31": "MG",
                "32": "ES",
                "33": "RJ",
                "35": "SP",
                "41": "PR",
                "42": "SC",
                "43": "RS",
                "50": "MS",
                "51": "MT",
                "52": "GO",
                "53": "DF"
            };
            prefix = "estado/";
        } else if (ctrl.markerType === 2) {
            prefix = "regiao/v2/";
            kmlArray = {"1": "NORTE", "2": "NORDESTE", "3": "SUDESTE", "4": "SUL", "5": "CENTRO-OESTE2"};
        }

        var src = "http://www2.sbc.org.br/mapas/shapes/" + prefix + kmlArray[id] + ".kml";
        ctrl.KMLAtual = new google.maps.KmlLayer(src, {
            suppressInfoWindows: true,
            preserveViewport: true,
            map: ctrl.googlemap
        });
    };

    this.changeMarkerType = function (ind) {
        ctrl.oldMarkerType = ctrl.markerType;
        ctrl.markerType = ind;
        if (ctrl.visualType === 0 && (ctrl.markerType === 1 || ctrl.markerType === 2)) {
            ctrl.visualType = 1;
            cUI.sidebarCtrl.setSelectedVisual(1);
            window.cUserConfig.close_dialog();
        } else if ((ctrl.visualType === 1 && (ctrl.markerType === 0))) {
            ctrl.visualType = 0;
            cUI.sidebarCtrl.setSelectedVisual(0);
            window.cUserConfig.close_dialog();
        }
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
            var fator = cUserConfig.data[2].cs[ctrl.markerType].vs.fator.valor;
            var min = cUserConfig.data[2].cs[ctrl.markerType].vs.min.valor;
            var opacity = cUserConfig.data[2].cs[ctrl.markerType].vs.opacity.valor;

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

        cData.requestMarkers(filters, ctrl.setData);
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

        const fator = cUserConfig.data[2].cs[ctrl.markerType].vs.fator.valor;
        const min = cUserConfig.data[2].cs[ctrl.markerType].vs.min.valor;
        const opacity = cUserConfig.data[2].cs[ctrl.markerType].vs.opacity.valor / 100.0;
        var count = -1;
        var markers = data.map(function (mun) {
            count++;
            if (typeof (google) !== "undefined") {
                var marker;
                if (ctrl.visualType === 2) {
                    marker = new google.maps.Circle({
                        strokeColor: ctrl.orange_circle.strokeColor,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: ctrl.orange_circle.fillColor,
                        fillOpacity: opacity,
                        label: {text: mun[0]},
                        map: ctrl.googlemap,
                        center: {lng: parseFloat(mun[1]), lat: parseFloat(mun[2])},
                        radius: mun[0] * fator + min,
                        zIndex: count
                    });
                } else {
                    var marker_data = {
                        position: {lng: parseFloat(mun[1]), lat: parseFloat(mun[2])},
                        icon: ctrl.orange_marker,
                        label: {
                            text: mun[0],
                            color: 'white',
                            fontSize: '12px',
                            x: '30',
                            y: '10'
                        },
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
                    cData.listCursos(cod_mun, filters, function (data) {
                        cUI.filterCtrl.close();
                        cUI.markerDialogCtrl.open({uf: uf_mun, cod_mun: cod_mun, name_mun: name_mun, data: data});
                    });
                });
            }
            return marker;
        });
        if (typeof (google) !== "undefined" && ctrl.visualType === 0) {
            ctrl.markerCluster = new MarkerClusterer(ctrl.googlemap, markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        }
        cUI.filterCtrl.enableFilters();
        $("#splash").remove();
    };

    this.HabiliarModoInstituicao = function (id_inst, sigla_inst, nome_inst) {
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
        window.cUserConfig.config_dialog(ctrl.visualType, ctrl.updateVisualType);
    };

    this.ShowVisualConfigDialog = function (event, data) {
        window.cUserConfig.config_dialog(data.ind, ctrl.updateVisualType);
    };

    this.closeFilterInstBtn.hide();
    this.selectsearch.change(this.onchangeSelectSearch);
    this.appendLeft(this.searchdiv);
    this.appendLeft(this.treeSelectMode);
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
            ctrl.changeVisualType(index);
            cUI.sidebarCtrl.setSelectedVisual(index);
        }
    });
    $("#marker-selected-text").selectmenu({
        change: function (event, ui) {
            var index = parseInt(ui.item.value);
            ctrl.changeMarkerType(index);
            cUI.sidebarCtrl.setSelectedMarker(index);
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
