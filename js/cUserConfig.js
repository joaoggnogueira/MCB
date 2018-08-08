(function () {

    ENUM_TIPOS = {
        numero: {
            id: 0
        }, boleano: {
            id: 1
        }, intervalo: {
            id: 2
        }, select: {
            id: 3
        }, color: {
            id: 4
        }
    };

    //value
    function v(titulo, valor_default, tipo, min, max, step, unit, chunckunit) {
        return {
            titulo: titulo,
            valor: valor_default,
            valor_default: valor_default,
            tipo: tipo,
            min: min,
            max: max,
            step: (step !== undefined ? step : 1),
            unit: (unit !== undefined ? unit : ""),
            chunckunit: (chunckunit !== undefined ? parseFloat(chunckunit) : 1)
        };
    }

    //categoria
    function c(titulo, vs) {
        return  {
            titulo: titulo,
            vs: vs
        };
    }

    //registro
    function r(titulo, subtitulo, childdesc, cs, fs) {
        return {
            titulo: titulo,
            subtitulo: subtitulo,
            childdesc: childdesc,
            cs: cs,
            fs: fs
        };
    }

    function format2AD(value) {
        return parseFloat(Math.round(value * 10) / 10).toFixed(1);
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

            var updateValue = function (valueBrute) {
                var value = parseFloat(valueBrute);
                if (input.chunckunit !== 1) {
                    value /= input.chunckunit;
                }
                handle.text(value + input.unit);
            };
            input.instance = $("<div/>")
                    .attr("id", id)
                    .append(handle)
                    .appendTo(tab)
                    .slider({min: input.min, max: input.max, step: input.step, value: input.valor,
                        create: function () {
                            updateValue($(this).slider("value"));
                        },
                        slide: function (event, ui) {
                            updateValue($(this).slider("value"));
                        },
                        stop: function (event, ui) {
                            updateValue($(this).slider("value"));
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
                    min: v("Raio mínimo", 5000, ENUM_TIPOS.numero, 100, 50000, 100, "km", 1000),
                    fator: v("Acréscimo do raio por unidade", 500, ENUM_TIPOS.numero, 0, 1000, 10, "km", 1000),
                    opacity: v("Opacidade", 40, ENUM_TIPOS.numero, 0, 100, 1, "%")
                }),
                c("Estado", {
                    min: v("Raio mínimo", 10000, ENUM_TIPOS.numero, 100, 50000, 100, "km", 1000),
                    fator: v("Acréscimo do raio por unidade", 100, ENUM_TIPOS.numero, 0, 1000, 10, "km", 1000),
                    opacity: v("Opacidade", 35, ENUM_TIPOS.numero, 0, 100, 1, "%")
                }),
                c("Região", {
                    min: v("Raio mínimo", 10000, ENUM_TIPOS.numero, 100, 50000, 100, "km", 1000),
                    fator: v("Acréscimo do raio por unidade", 100, ENUM_TIPOS.numero, 0, 1000, 10, "km", 1000),
                    opacity: v("Opacidade", 30, ENUM_TIPOS.numero, 0, 100, 1, "%")
                })
            ])

        }
    };

}());