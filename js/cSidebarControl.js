

function cSidebarControl() {

    this.sidebar = cUI.catchElement("sidebar");
    this.theater = cUI.catchElement("theater-sidebar");
    this.sidebarBtn = cUI.catchElement("btn-toggle-sidebar");
    this.randomTheme = cUI.catchElement("random-theme-btn");
    this.sidebarBtn.child("i").setStates(["fa-bars", "fa-times"]);

    this.filter_btn = cUI.catchElement("sidebar-filters-btn");
    this.markers_btn = cUI.catchElement("sidebar-markers-btn");
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

    this.showMarkersTheater = function () {
        ctrl.showTheater("theater-markers");
    };

    this.showHelpTheater = function () {
        ctrl.showTheater("theater-help");
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

        ctrl.showTheater("theater-save");
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
                ]
            });
            ctrl.showTheater("theater-open");

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

    this.showTheater = function (id) {
        if (id !== ctrl.selectedTheater.id) {
            var theater = cUI.catchElement("theater-content").child("#" + id);
            ctrl.selectedTheater.fadeOut(400, function () {
                theater.fadeIn(400);
            });
            ctrl.selectedTheater = theater;
        }
    };

    this.btnMarkerEvent = function (event, data) {
        ctrl.toggle();
        cUI.mapCtrl.changeMarkerType(data.ind);
    };

    this.filter_btn.click(this.showFilters);
    this.sidebarBtn.click(this.toggle);
    this.randomTheme.click(this.nextTheme);
    this.markers_btn.click(this.showMarkersTheater);
    this.help_btn.click(this.showHelpTheater);
    this.save_btn.click(this.showSaveTheater);
    this.load_btn.click(this.loadData);
    this.ok_save.click(this.saveData);

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

    var buttonlist = theaterslist[1].childlist("button");
    for (var i = 0; i < buttonlist.length; i++) {
        buttonlist[i].click(this.btnMarkerEvent, {ind: i});
    }

}