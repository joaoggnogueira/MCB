
function cMarkerDialogControl() {

    this.dialog = cUI.catchElement("marker-dialog");
    this.datatable = null;
    this.copytable = null;
    this.close_btn = this.dialog.child(".close-btn");
    this.notebook = new cNotebookControl("notebook-marker-dialog");
    this.theater = cUI.catchElement("theater-details");
    var ctrl = this;

    this.showTheater = function (html) {
        ctrl.theater.show();
        ctrl.theater.child(".modal-content").innerHTML = html;
    };

    this.closeTheater = function () {
        ctrl.theater.hide();
        ctrl.theater.child(".modal-content").innerHTML = "";
    };

    this.open = function (data) {
        ctrl.dialog.slideDown(400);

        var local = data.name_mun;

        if (data.uf.length === 2) {
            local += " (" + data.uf + ")";
        }

        cUI.catchElement("name-mun").html(local);
        cUI.catchElement("cod-mun").html(data.cod_mun);
        if (ctrl.datatable) {
            ctrl.datatable.destroy(true);
            cUI.catchElement("cursos-tab").innerHTML = ctrl.copytable;
        } else {
            ctrl.copytable = cUI.catchElement("cursos-tab").innerHTML;
        }

        var list = [];
        for (var key in data.data) {
            var row = data.data[key];
            list[key] = [];
            list[key][0] = row[0];
            list[key][1] = row[1];
            if (row[2].length > 2) {
                list[key][2] = row[2];
            } else {
                list[key][2] = row[3];
            }
        }

        ctrl.datatable = $("#table-cursos").DataTable({
            data: list,
            "columnDefs": [
                {"visible": false, "searchable": false, "targets": 0}
            ],
            "language": {
                "sEmptyTable": "Nenhum curso encontrado",
                "sInfo": "De _START_ até _END_ de _TOTAL_ cursos",
                "sInfoEmpty": "",
                "sInfoFiltered": "(Filtrados de _MAX_ cursos)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ cursos por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum curso encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            }
        });
        $('#table-cursos tbody').on('click', 'tr', function () {
            var row = ctrl.datatable.row(this).data();
            var id_curso = row[0];
            cData.getDetailsHTML(id_curso, function (data_enade) {
                ctrl.showTheater(data_enade.view);
                new cNotebookControl("details-dialog");
                ctrl.theater.child(".btn-close").click(ctrl.closeTheater);

                var inst_nome = data_enade.nome_instituicao;
                if (data_enade.sigla_instituicao.length > 0 && data_enade.sigla_instituicao.length < 10) {
                    inst_nome += " (" + data_enade.sigla_instituicao + ")";
                }
                local = unescape(encodeURIComponent(local));
                var notfound = $("#campus_enade > option[value='" + data_enade.cod_mun + "']").length === 0;
                if (notfound) {
                    $("#campus_enade > .first-option").text("Não foi possível encontrar nenhuma avaliação do ENADE (para o cursos de computação) do campus " + local.toUpperCase() + " da " + inst_nome.toUpperCase() + ", porém é possível ver o resultado de outros campus cliquando aqui!");
                }

                $("#theater-details .controlgroup_vertical").controlgroup({"direction": "vertical"});
                $("#theater-details .ui-widget").css("width", "460px");

                var area_enade_selected = function () {

                    var area = $("#area_enade").val();
                    var campus = $("#campus_enade").val();
                    var ano = $("#ano_enade").val();
                    if (area !== null && campus !== null && ano !== null) {
                        cData.getAvaliacaoEnade(area, campus, ano, data_enade.id_inst, function (data) {
                            var details = $("#enade-details");
                            for (var key in data) {
                                var value = data[key];
                                details.find("span[name='" + key + "']").text(value);
                            }
                            details.show();
                        });
                    }
                };

                var ano_enade_selected = function () {
                    cData.listAreaEnade(data_enade.id_inst, $("#campus_enade").val(), $("#ano_enade").val(), id_curso, function (data) {
                        $("#area_enade > option:not(.first-option)").remove();
                        $("#area_enade").removeAttr("disabled");
                        $("label[for='area_enade-button']").removeAttr("disabled");
                        for (var i = 0; i < data.list.length; i++) {
                            $("<option/>").attr("value", data.list[i].id).text(data.list[i].nome).appendTo($("#area_enade"));
                        }
                        $("#area_enade").val(data.inducao).selectmenu("refresh");
                        if (data.inducao != -1) {
                            area_enade_selected();
                        }
                    });
                };

                var campus_enade_selected = function () {
                    cData.listAnoEnade(data_enade.id_inst, $("#campus_enade").val(), function (data) {
                        $("#ano_enade > option:not(.first-option)").remove();
                        $("#ano_enade").removeAttr("disabled");
                        $("label[for='ano_enade-button']").removeAttr("disabled");

                        $("#area_enade > option:not(.first-option)").remove();
                        $("label[for='area_enade-button']").attr("disabled");
                        $("#area_enade").attr("disabled", true).selectmenu("refresh");
                        for (var i = 0; i < data.length; i++) {
                            $("<option/>").attr("value", data[i].ano).text(data[i].ano).appendTo($("#ano_enade"));
                        }
                        $("#ano_enade").val(data[data.length - 1].ano).selectmenu("refresh");
                        ano_enade_selected();
                    });
                };

                $("#area_enade").on("selectmenuchange", area_enade_selected);
                $("#campus_enade").on("selectmenuchange", campus_enade_selected);
                $("#ano_enade").on("selectmenuchange", ano_enade_selected);

                if (notfound) {
                    $("#campus_enade-button").addClass("ui-selectmenu-button-pre-wrap").click();
                    $("#campus_enade-menu .ui-state-disabled").hide();
                    $("#campus_enade-button").click();
                } else {
                    $("#campus_enade").val(data_enade.cod_mun).selectmenu("refresh");
                    campus_enade_selected();
                }
            });
        });
        $(".graph-content svg").remove();
        $(".graph-content .legenda").remove();
        cGraph("graph-content-grau", data.cod_mun);
        cGraph("graph-content-rede", data.cod_mun);
        cGraph("graph-content-modalidade", data.cod_mun);
        cGraph("graph-content-natureza", data.cod_mun);
        cGraph("graph-content-naturezadep", data.cod_mun);
        cGraph("graph-content-nivel", data.cod_mun);
        cGraph("graph-content-programa", data.cod_mun);
        cGraph("graph-content-tipoorganizacao", data.cod_mun);
        cGraph("graph-content-enade", data.cod_mun);
        cGraph("graph-content-estado", data.cod_mun);

    };

    this.close = function () {
        ctrl.dialog.slideUp(400);
    };

    $("#graphs-tab ul").sortable({
        items: "> li",
        handle: ".fa-ellipsis-v.draggable-sortable-btn"
    });

    this.close_btn.click(this.close);
    this.dialog.hide();
    this.theater.hide();
}