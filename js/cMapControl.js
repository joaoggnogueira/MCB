
/* global google */

function cMapControl() {

    this.mapdiv = cUI.catchElement("map");
    this.searchdiv = cUI.catchElement("input-group-search");
    this.inputsearchmun = cUI.catchElement("pac-input");
    this.inputsearchinst = cUI.catchElement("inst-input");
    this.selectsearch = cUI.catchElement("select-search");
    this.closeFilterInstBtn = cUI.catchElement("close-filter-inst-btn");
    this.iconSearch = cUI.catchElement("icon-search");
    this.treeSelectMode = cUI.catchElement("selected-mode");

    this.hashMarkers = false;
    this.markerCluster = false;
    this.visualType = 0;
    this.markerType = 0;
    this.instModeId = false;

    this.shapes = [];

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

    this.changeMarkerType = function (ind) {
        ctrl.oldMarkerType = ctrl.markerType;
        ctrl.markerType = ind;
        if (ctrl.visualType === 0 && (ctrl.markerType === 1 || ctrl.markerType === 2)) {
            ctrl.visualType = 1;
            cUI.sidebarCtrl.setSelectedVisual(1);
        }
        if ((ctrl.visualType === 1 && (ctrl.markerType === 0))) {
            ctrl.visualType = 0;
            cUI.sidebarCtrl.setSelectedVisual(0);
        }
        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
    };

    this.changeVisualType = function (ind) {
        ctrl.visualType = ind;
        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
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

    this.setData = function (data) {
        
        if(ctrl.oldMarkerType !== ctrl.markerType){
            if(ctrl.shapes) {
                for (var key in ctrl.shapes) {
                    if (ctrl.shapes[key].setMap) {
                        ctrl.shapes[key].setMap(null);
                    }
                }
            }
            ctrl.shapes.length = 0;
            ctrl.shapes = [];
        }
        
        if (ctrl.markerType === 1) {
            var regiaoKML = ["all2"];
            for(var i=0;i<regiaoKML.length;i++){
                var src = "https://portaldoprofessor.fct.unesp.br/projetos/mcb/shapes/estado/"+regiaoKML[i]+".kml";
                var kmlLayer = new google.maps.KmlLayer(src , {
                    suppressInfoWindows: true,
                    preserveViewport: true,
                    map: ctrl.googlemap
                });
                ctrl.shapes.push(kmlLayer);
            }
        } else if (ctrl.markerType === 2) {
            
            var regiaoKML = ["SUL","CENTRO-OESTE","NORTE03","NORDESTE","SUDESTE"];
            for(var i=0;i<regiaoKML.length;i++){
                var src = "https://portaldoprofessor.fct.unesp.br/projetos/mcb/shapes/regiao/fill/"+regiaoKML[i]+".kml";
                var kmlLayer = new google.maps.KmlLayer(src , {
                    suppressInfoWindows: true,
                    preserveViewport: true,
                    map: ctrl.googlemap
                });
                ctrl.shapes.push(kmlLayer);
            }
        }
//        var kmlLayer = new google.maps.KmlLayer("https://portaldoprofessor.fct.unesp.br/projetos/mcb/shapes/test.kml", {
//            suppressInfoWindows: true,
//            preserveViewport: true,
//            map: ctrl.googlemap
//        });
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
                if (ctrl.visualType === 2) {
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
                            x: '30',
                            y: '70'
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
                marker.addListener('click', function () {
                    var filters = cUI.filterCtrl.getFilters();
                    if (ctrl.instModeId === false) {
                        filters.instituicao = {
                            all: true
                        };
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
    };

    this.HabiliarModoInstituicao = function (id_inst, sigla_inst, nome_inst) {
        cUI.filterCtrl.resetFilters(false);
        ctrl.instModeId = id_inst;
        ctrl.requestUpdate(cUI.filterCtrl.getFilters());
        ctrl.closeFilterInstBtn.child(".pac-addon-header").html(sigla_inst);
        ctrl.closeFilterInstBtn.child(".pac-addon-content").html(nome_inst);
        ctrl.closeFilterInstBtn.show();
        ctrl.inputsearchinst.hide();
        ctrl.selectsearch.hide();
        ctrl.iconSearch.hide();
    };

    this.DesabilitarModoInstituicao = function () {
        if (ctrl.instModeId !== false) {
            ctrl.instModeId = false;
            ctrl.requestUpdate(cUI.filterCtrl.getFilters());
            ctrl.closeFilterInstBtn.hide();
            ctrl.inputsearchinst.show();
            ctrl.selectsearch.show();
            ctrl.iconSearch.show();
            ctrl.inputsearchinst.value = "";
            cUI.markerDialogCtrl.close();
        }
    };

    this.closeFilterInstBtn.hide();
    this.selectsearch.change(this.onchangeSelectSearch);
    this.appendLeft(this.searchdiv);
    this.appendLeft(this.treeSelectMode);
    var options = {
        componentRestrictions: {country: 'br'}
    };
    var autocomplete = new google.maps.places.Autocomplete(this.inputsearchmun, options);
    autocomplete.bindTo('bounds', this.googlemap);

    ctrl.autocomplete = autocomplete;

    autocomplete.addListener('place_changed', ctrl.buscar);

    this.closeFilterInstBtn.click(this.DesabilitarModoInstituicao);
    
    $("#visual-selected-text").selectmenu({
        change:function(event,ui){
            var index = parseInt(ui.item.value); 
            ctrl.changeVisualType(index);
            cUI.sidebarCtrl.setSelectedVisual(index);
        }
    });
    $("#marker-selected-text").selectmenu({
        change:function(event,ui){
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

}