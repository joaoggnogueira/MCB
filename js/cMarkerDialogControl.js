'use strict';
function cMarkerDialogControl() {

    this.dialog = cUI.catchElement("marker-dialog");
    this.datatable = null;
    this.copytable = null;
    this.close_btn = this.dialog.child(".close-btn");
    this.notebook = new cNotebookControl("notebook-marker-dialog");
    this.theater = cUI.catchElement("theater-details");
    this.description = this.dialog.child(".description");
    this.alert = this.dialog.child(".alert");
    this.tabheadermun = this.dialog.child("#tabheadermun");
    this.localtab = this.dialog.child("#local-tab");
    this.atual_data = false;
    const ctrl = this;

    this.showTheater = function (html) {
        ctrl.theater.show();
        ctrl.theater.child(".modal-content").innerHTML = html;
    };

    this.closeTheater = function () {
        ctrl.theater.hide();
        ctrl.theater.child(".modal-content").innerHTML = "";
    };

    this.open = function (data) {
        ctrl.atual_data = data;
        ctrl.dialog.slideDown(400);
        switch (cUI.mapCtrl.markerType) {
            case 0:
                ctrl.description.cText("Mostrando resultados para o Município");
                ctrl.tabheadermun.cText("Município");
                break;
            case 1:
                ctrl.description.cText("Mostrando resultados para o Estado");
                ctrl.tabheadermun.cText("Estado (UF)");
                break;
            case 2:
                ctrl.description.cText("Mostrando resultados para a Região");
                ctrl.tabheadermun.cText("Região");
                break;
        }
        if ($("#counter-filters").find(".total").text() !== "") {
            this.alert.show();
        } else {
            this.alert.hide();
        }

        let local = data.name_mun;

        if (data.uf.length === 2) {
            local += " (" + data.uf + ")";
        }
        cUI.catchElement("name-mun").html(local);
        if (ctrl.datatable) {
            ctrl.datatable.destroy(true);
            cUI.catchElement("cursos-tab").innerHTML = ctrl.copytable;
        } else {
            ctrl.copytable = cUI.catchElement("cursos-tab").innerHTML;
        }

        const list = data.data.map((row) => {
            const row_saida = [];
            if (row[4] === "2") {
                row[1] += " *";
            }
            row_saida[0] = row[0];
            row_saida[1] = row[1];
            if (row[2].length > 2) {
                row_saida[2] = row[2];
            } else {
                row_saida[2] = row[3];
            }
            row_saida[3] = row[5];
            return row_saida;
        });

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
            const row = ctrl.datatable.row(this).data();
            const id_curso = row[0];
            cData.getCursoDetailsHTML(id_curso, function (data) {
                ctrl.showTheater(data.view);

                const quill_adicionais = new Quill('#rich_editor', {
                    readOnly: true,
                    placeholder: 'Escreva aqui informações adicionais sobre o curso',
                });
                const a = document.createElement("div");
                a.innerHTML = data.adicional;
                if (data.adicional) {
                    quill_adicionais.setContents(JSON.parse(a.textContent.replace(/\n/g, "\\n")));
                } else {
                    quill_adicionais.setContents({"ops": [{"attributes": {"underline": true, "color": "#bbbbbb", "italic": true}, "insert": "Sem informações"}, {"attributes": {"header": 3}, "insert": "\n"}]});
                }
                new cNotebookControl("details-dialog");
                ctrl.theater.child(".btn-close").click(ctrl.closeTheater);
                let inst_nome = false;
                if (data.sigla_instituicao.length > 0 && data.sigla_instituicao.length < 10) {
                    inst_nome = (data.nome_instituicao + " (" + data.sigla_instituicao + ")").toUpperCase();
                }
                local = unescape(encodeURIComponent(local)).toUpperCase();
                const notfound = $("#campus_enade > option[value='" + data.cod_mun + "']").length === 0;
                if (notfound) {
                    $("#campus_enade > .first-option").text("Não foi possível encontrar nenhuma avaliação do ENADE (para o cursos de computação) do campus " + local + " da " + inst_nome + ", porém é possível ver o resultado de outros campus cliquando aqui!");
                }
                const theater = $("#theater-details");
                theater.find(".controlgroup_vertical").controlgroup({"direction": "vertical"});
                theater.find(".ui-widget").css("width", "460px");

                const area_enade_selected = function () {

                    const area = $("#area_enade").val();
                    const campus = $("#campus_enade").val();
                    const ano = $("#ano_enade").val();
                    if (area !== null && campus !== null && ano !== null) {
                        cData.getAvaliacaoEnade(area, campus, ano, data.id_inst, function (data) {
                            const details = $("#enade-details");
                            for (let key in data) {
                                const value = data[key];
                                details.find("span[name='" + key + "']").text(value);
                            }
                            details.show();
                        });
                    }
                };

                const ano_enade_selected = function () {
                    cData.listAreaEnade(data.id_inst, $("#campus_enade").val(), $("#ano_enade").val(), id_curso, function (data) {
                        $("#area_enade > option:not(.first-option)").remove();
                        $("#area_enade").removeAttr("disabled");
                        $("label[for='area_enade-button']").removeAttr("disabled");
                        for (let i = 0; i < data.list.length; i++) {
                            $("<option/>").attr("value", data.list[i].id).text(data.list[i].nome).appendTo($("#area_enade"));
                        }
                        $("#area_enade").val(data.inducao).selectmenu("refresh");
                        if (data.inducao != -1) {
                            area_enade_selected();
                        }
                    });
                };

                const campus_enade_selected = function () {
                    cData.listAnoEnade(data.id_inst, $("#campus_enade").val(), function (data) {
                        $("#ano_enade > option:not(.first-option)").remove();
                        $("#ano_enade").removeAttr("disabled");
                        $("label[for='ano_enade-button']").removeAttr("disabled");

                        $("#area_enade > option:not(.first-option)").remove();
                        $("label[for='area_enade-button']").attr("disabled");
                        $("#area_enade").attr("disabled", true).selectmenu("refresh");
                        for (let i = 0; i < data.length; i++) {
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
                    $("#campus_enade").val(data.cod_mun).selectmenu("refresh");
                    campus_enade_selected();
                }
            });
        });
        const mapInfo = ctrl.atual_data.mapInfo;

        $(".graph-content svg").remove();
        $(".graph-content .text-one").remove();
        $(".graph-content .legenda").remove();
        cGraph(mapInfo, "graph-content-grau", data.cod_mun);
        cGraph(mapInfo, "graph-content-rede", data.cod_mun);
        cGraph(mapInfo, "graph-content-modalidade", data.cod_mun);
        cGraph(mapInfo, "graph-content-natureza", data.cod_mun);
        cGraph(mapInfo, "graph-content-naturezadep", data.cod_mun);
        cGraph(mapInfo, "graph-content-nivel", data.cod_mun);
        cGraph(mapInfo, "graph-content-programa", data.cod_mun);
        cGraph(mapInfo, "graph-content-tipoorganizacao", data.cod_mun);
        cGraph(mapInfo, "graph-content-enade", data.cod_mun);
        cGraph(mapInfo, "graph-content-estado", data.cod_mun);
        ctrl.localtab.html("Carregando ...");
        cData.getMunicipioDetailsHTML(data.cod_mun, cUI.mapCtrl.markerType, function (data) {
            ctrl.localtab.html(data.view);
        });
    };

    this.close = function () {
        ctrl.dialog.slideUp(400);
        cUI.mapCtrl.unselectMarker();
    };

    $("#graphs-tab ul").sortable({
        items: "> li",
        handle: ".fa-ellipsis-v.draggable-sortable-btn"
    });
    this.close_btn.click(this.close);
    this.dialog.hide();
    this.theater.hide();
}