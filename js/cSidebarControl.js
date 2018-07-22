

function cSidebarControl() {

    this.sidebar = cUI.catchElement("sidebar");
    this.theater = cUI.catchElement("theater-sidebar");
    this.sidebarBtn = cUI.catchElement("btn-toggle-sidebar");
    this.randomTheme = cUI.catchElement("random-theme-btn");
    this.sidebarBtn.child("i").setStates(["fa-bars", "fa-times"]);

    this.filter_btn = cUI.catchElement("sidebar-filters-btn");
    this.visualizacao_btn = cUI.catchElement("sidebar-visualizacao-btn");
    this.estatisticas_btn = cUI.catchElement("sidebar-estatistica-btn");
    this.marcador_btn = cUI.catchElement("sidebar-markers-btn");
    this.help_btn = cUI.catchElement("sidebar-help-btn");
    this.save_btn = cUI.catchElement("sidebar-save-btn");
    this.load_btn = cUI.catchElement("sidebar-load-btn");

    this.ok_save = cUI.catchElement("ok-save");

    var ctrl = this;

    this.toggle = function () {
        ctrl.sidebar.toggleSlideHorizontal(200);
        ctrl.theater.toggleFade(200);
        ctrl.sidebarBtn.child("i").nextState();
        cUI.filterCtrl.fadeOut(200);
        cUI.markerDialogCtrl.close();
        cUI.mapCtrl.DesabilitarModoInstituicao();
        ctrl.showTheater("theater-overview");
    };

    this.hide = function () {
        ctrl.sidebar.hide();
        ctrl.theater.hide();
    };

    this.nextTheme = function () {
        cUI.body.nextState();
    };

    this.showFilters = function () {
        ctrl.toggle();
        cUI.filterCtrl.show();
    };
    
    this.showEstatisticasTheater = function(){
        window.open("./RelatorioComputacaoINEP2016.pdf",'_blank');
    }

    this.showMarkersTheater = function () {
        ctrl.showTheater("theater-markers", ctrl.marcador_btn);
    };

    this.showVisualizacaoTheater = function () {
        ctrl.showTheater("theater-visualizacao", ctrl.visualizacao_btn);
    };

    this.showHelpTheater = function () {
        ctrl.showTheater("theater-help", ctrl.help_btn);
    };

    this.showSaveTheater = function () {
        var tipo = "";
        switch (cUI.mapCtrl.markerType) {
            case 0:
                tipo = "Com agrupamento";
                break;
            case 1:
                tipo = "Sem agrupamento";
                break;
            case 2:
                tipo = "Círculo Ponderado";
                break;
        }
        var list = cUI.catchElement("filter-list").childlist(".filter-type.enabled");
        var htmlfilter = "";
        for (var i = 0; i < list.length; i++) {
            var title = list[i].child(".title");
            htmlfilter += "<div class='badge'>" + title.innerHTML + "</div>";
        }
        if (list.length === 0) {
            htmlfilter += "<div class='badge'>NENHUM</div>";
        }

        var markersave = cUI.catchElement("marker-to-save");
        var filtersave = cUI.catchElement("filters-to-save");

        markersave.html("<div class='badge'>" + tipo + "</div><div class='badge badge-edit'>Editar</div>");
        filtersave.html(htmlfilter + "<div class='badge badge-edit'>Editar</div>");

        markersave.child(".badge-edit").click(ctrl.showMarkersTheater);
        filtersave.child(".badge-edit").click(ctrl.showFilters);

        ctrl.showTheater("theater-save", ctrl.save_btn);
    };

    this.saveData = function () {
        var rotulo = cUI.catchElement("rotulo").value;
        if (rotulo.length === "") {
            swal({type: "warning", text: "O rótulo não pode ser vazio"});
        } else {
            var filter = cUI.filterCtrl.getFilters();
            var markertype = cUI.mapCtrl.getMarkerType();
            var data = {filter: filter, markertype: markertype};
            cData.saveConfiguracoes(rotulo, data);
        }
    };

    this.loadData = function () {
        cData.listConfiguracoes(function (datarequest) {

            var data = [];
            ctrl.listLoaded = [];
            ctrl.listLoaded.length = 0;

            for (var i = 0; i < datarequest.length; i++) {
                var row = datarequest[i];
                data[i] = [];

                data[i][0] = row[0];
                data[i][1] = row[1].toUpperCase();

                var json = JSON.parse(row[2]);
                var markertype = null;
                ctrl.listLoaded[row[0]] = json;

                switch (json.markertype) {
                    case 0:
                        markertype = "Com agrupamento";
                        break;
                    case 1:
                        markertype = "Sem agrupamento";
                        break;
                    case 2:
                        markertype = "Círculo Ponderado";
                        break;
                }
                data[i][2] = markertype;
                data[i][3] = row[3];

            }
            if (ctrl.datatable) {
                ctrl.datatable.destroy(true);
                cUI.catchElement("theater-table-load").innerHTML = ctrl.copytable;
            } else {
                ctrl.copytable = cUI.catchElement("theater-table-load").innerHTML;
            }

            ctrl.datatable = $("#table-load").DataTable({
                data: data,
                "autoWidth": false,
                "columnDefs": [
                    {"width": "20px", "className": "text-center", "targets": 0},
                    {"width": "170px", "targets": 2},
                    {"width": "150px", "targets": 3}
                ],
                "language": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ resultados por página",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": "Pesquisar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                }
            });
            ctrl.showTheater("theater-open", ctrl.load_btn);

            $('#table-load tbody').on('click', 'tr', function () {
                var row = ctrl.datatable.row(this).data();
                var id = row[0];
                cUI.filterCtrl.setFilters(ctrl.listLoaded[id].filter);
                cUI.mapCtrl.markerType = ctrl.listLoaded[id].markertype;
                ctrl.toggle();
                cUI.mapCtrl.requestUpdate(cUI.filterCtrl.getFilters());
            });
        });
    };

    this.showTheater = function (id, source) {

        var buttonlist = ctrl.sidebar.childlist(".sidebar-button");

        for (var i = 0; i < buttonlist.length; i++) {
            buttonlist[i].classList.remove("selected");
        }
        if (source) {
            source.classList.add("selected");
        }
        if (id !== ctrl.selectedTheater.id) {
            var theater = cUI.catchElement("theater-content").child("#" + id);

            var theaterslist = cUI.catchElement("theater-content").childlist(".theater-about");
            for (var i = 0; i < theaterslist.length; i++) {
                theaterslist[i].hide();
            }
            theater.show();

            ctrl.selectedTheater = theater;
        }
    };

    this.btnVisualEvent = function (event, data) {
        cUI.mapCtrl.changeVisualType(data.ind);
        ctrl.setSelectedVisual(data.ind);
        ctrl.toggle();
    };

    this.btnMarkerEvent = function (event, data) {
        cUI.mapCtrl.changeMarkerType(data.ind);
        ctrl.setSelectedMarker(data.ind);
        ctrl.toggle();
    };

    this.setSelectedVisual = function(ind){
        for (var i = 0; i < ctrl.btn_list_visualizacao.length; i++) {
           ctrl.btn_list_visualizacao[i].enable();
        }
        ctrl.btn_list_visualizacao[ind].disable();
    };
    this.setSelectedMarker = function(ind){
        for (var i = 0; i < ctrl.btn_list_marker.length; i++) {
           ctrl.btn_list_marker[i].enable();
        } 
        ctrl.btn_list_marker[ind].disable();
    };
        
    this.filter_btn.click(this.showFilters);
    this.sidebarBtn.click(this.toggle);
    this.randomTheme.click(this.nextTheme);
    this.visualizacao_btn.click(this.showVisualizacaoTheater);
    this.marcador_btn.click(this.showMarkersTheater);
    this.help_btn.click(this.showHelpTheater);
    this.save_btn.click(this.showSaveTheater);
    this.load_btn.click(this.loadData);
    this.ok_save.click(this.saveData);
    this.estatisticas_btn.click(this.showEstatisticasTheater);
    
    this.hide();

    var theaterslist = cUI.catchElement("theater-content").childlist(".theater-about");
    this.selectedTheater = theaterslist[0];

    var first = true;
    for (var i = 0; i < theaterslist.length; i++) {
        if (first) {
            first = false;
        } else {
            theaterslist[i].style.display = "none";
        }
    }
    
    var buttonlistVisualizacao = theaterslist[1].childlist(".button-addon-theater");
    for (var i = 0; i < buttonlistVisualizacao.length; i++) {
        buttonlistVisualizacao[i].click(cUI.mapCtrl.ShowVisualConfigDialog, {ind: i});
    }
    
    var buttonlistVisualizacao = theaterslist[1].childlist(".button-toggle-theater");
    for (var i = 0; i < buttonlistVisualizacao.length; i++) {
        buttonlistVisualizacao[i].click(this.btnVisualEvent, {ind: i});
    }

    var buttonlistMarker = theaterslist[2].childlist(".button-toggle-theater");
    for (var i = 0; i < buttonlistMarker.length; i++) {
        buttonlistMarker[i].click(this.btnMarkerEvent, {ind: i});
    }
    
    this.btn_list_visualizacao = buttonlistVisualizacao;
    this.btn_list_marker = buttonlistMarker;
}