(function () {

    ENUM_TIPOS = {
        inteiro: {
            id: 0
        }, real: {
            id: 1
        }, boleano: {
            id: 2
        }, intervalo_inteiro: {
            id: 3
        }, intervalo_real: {
            id: 4
        }, select: {
            id: 5
        }, color: {
            id: 6
        }
    };

    //value
    function v(titulo, valor_default, tipo, min, max, step, unit) {
        return {titulo: titulo, valor: valor_default, valor_default: valor_default, tipo: tipo, min: min, max: max, step: step, unit: unit, level: "entrada"};
    }

    //categoria
    function c(titulo, vs) {
        return  {titulo: titulo, vs: vs, level: "categoria"};
    }

    //registro
    function r(titulo, subtitulo, childdesc, cs, fs) {
        return {titulo: titulo, subtitulo: subtitulo, childdesc: childdesc, cs: cs, fs: fs, level: "raiz"};
    }

    window.cUserConfig = {
        save: function () {

        },
        load: function () {

        },
        close_dialog() {
            $("#modal-dialog-config").dialog("destroy").remove();
        },
        revert_input: function (input) {
            input.instance.slider("option", "value", input.valor_default);
            input.valor = input.valor_default;
            $(input.instance).slider('option', 'stop').call(input.instance);
            $(input.instance).slider('option', 'slide').call(input.instance);

        },
        revert_category: function (categoria) {
            var inputs = categoria.vs;
            for (var key in inputs) {
                cUserConfig.revert_input(inputs[key]);
            }
        },
        input_insert_dialog: function (id, input, tab, onchange) {
            $("<label/>").attr("for", id).addClass("ui-controlgroup-label").html(input.titulo).appendTo(tab);
            var handle = $("<div/>").addClass("ui-slider-handle").css("margin-left", "-25px").css("width", "3em").css("text-align", "center");
            input.instance = $("<div/>")
                    .attr("id", id)
                    .append(handle)
                    .appendTo(tab)
                    .slider({min: input.min, max: input.max, step: input.step, value: input.valor,
                        create: function () {
                            handle.text((parseInt($(this).slider("value"))/1000.0)+input.unit);
                        },
                        slide: function (event, ui) {
                            if (event && ui) {
                                handle.text((parseInt(ui.value)/1000.0)+input.unit);
                            } else {
                                handle.text((parseInt($(this).slider("value"))/1000.0)+input.unit);
                            }
                        },
                        stop: function (event, ui) {
                            if (event && ui) {
                                input.valor = parseInt(ui.value);
                            }
                            onchange();
                        }
                    });

            $("<br/>").appendTo(tab);
        },
        category_insert_dialog: function (id, categoria, tabheaders, tabs, onchange) {
            $("<div/>").addClass("tab-header").html(categoria.titulo).appendTo(tabheaders);
            var tab = $("<div/>").addClass("tab").appendTo(tabs);
            var inputs = categoria.vs;
            for (var key in inputs) {
                cUserConfig.input_insert_dialog(key + "_" + id, inputs[key], tab, onchange);
            }
            $("<button/>")
                    .css("width", "max-content")
                    .html("Restaurar")
                    .button({icon: "ui-icon-refresh"})
                    .appendTo(tab)
                    .click(function () {
                        cUserConfig.revert_category(categoria);
                    });
            tab.controlgroup({"direction": "vertical"});
        },
        content_insert_dialog: function (data, onchange) {
            var notebook = $("<div/>").attr("id", "modal-dialog-config").addClass("notebook");
            $("<div/>").addClass("subtitle").html(data.subtitulo).appendTo(notebook);
            var tabheaders = $("<div/>").addClass("tabs-header").html(data.childdesc).appendTo(notebook);
            var tabs = $("<div/>").addClass("tabs").appendTo(notebook);

            var categorias = data.cs;

            for (var i = 0; i < categorias.length; i++) {
                cUserConfig.category_insert_dialog(i, categorias[i], tabheaders, tabs, onchange);
            }
            notebook.dialog({
                title: data.titulo,
                minWidth: 400,
                position: {my: "right-10 top+10", at: "right-10 top+10", of: window}
            });
            cNotebookControl(cUI.catchElement(notebook[0]));
        },
        empty_content_insert_dialog: function (data) {
            var notebook = $("<div/>").attr("id", "modal-dialog-config").addClass("notebook");
            $("<div/>").addClass("subtitle").html(data.subtitulo).appendTo(notebook);
            $("<div/>").addClass("empty").html("Nenhuma configuração disponível para este modo").appendTo(notebook);
            notebook.dialog({
                title: data.titulo,
                minWidth: 380,
                position: {my: "right-10 top+10", at: "right-10 top+10", of: window}
            });
        },
        config_dialog: function (config_id, onchange) {
            cUserConfig.close_dialog();
            var data = cUserConfig.data[config_id];
            if (data.cs) {
                cUserConfig.content_insert_dialog(data, onchange);
            } else {
                cUserConfig.empty_content_insert_dialog(data);
            }
            $(".ui-button.ui-corner-all.ui-widget.ui-button-icon-only.ui-dialog-titlebar-close").html("<i class='fa fa-times'></i>").css("text-indent", "0");
        },
        data: {
            0: r("Configuração de visualização", "Marcadores com Agrupamento"),
            1: r("Configuração de visualização", "Marcadores sem Agrupamento"),
            2: r("Configuração de visualização", "Circulo Ponderado", "Para o modo de: ", [
                c("Município", {
                    min: v("Raio mínimo", 5000, ENUM_TIPOS.inteiro, 100, 50000, 100, "km"),
                    fator: v("Acréscimo do raio por unidade", 500, ENUM_TIPOS.inteiro, 0, 1000, 10, "km")
                }),
                c("Estado", {
                    min: v("Raio mínimo", 10000, ENUM_TIPOS.inteiro, 100, 50000, 100, "km"),
                    fator: v("Acréscimo do raio por unidade", 100, ENUM_TIPOS.inteiro, 0, 1000, 10, "km")
                }),
                c("Região", {
                    min: v("Raio mínimo", 10000, ENUM_TIPOS.inteiro, 100, 50000, 100, "km"),
                    fator: v("Acréscimo do raio por unidade", 100, ENUM_TIPOS.inteiro, 0, 1000, 10, "km")
                })
            ])

        }
    };

}());