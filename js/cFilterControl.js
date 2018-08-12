
function cFilterControl() {

    this.filterbar = cUI.catchElement("filterbar");
    this.togglefilterbar = cUI.catchElement("btn-toggle-filter");
    this.resetBtn = cUI.catchElement("reset-filter");
    this.autoupdateCheckbox = cUI.catchElement("keep-update-filter");
    this.autoupdateCheckbox.checked = true;
    this.visiblefilters = false;
    this.filterCheckboxes = [];
    this.counterFilters = cUI.catchElement("counter-filters");

    this.counterFilters.hide();

    var ctrl = this;

    this.updateFiltersActived = function () {
        var listfilters = ctrl.filterbar.childlist(".filter-type.enabled");
        var total = listfilters.length;
        var countertotal = ctrl.counterFilters;
        var totaldiv = countertotal.child(".total");
        var textdiv = countertotal.child(".text");

        if (total === 0) {
            countertotal.hide();
            totaldiv.html("");
            textdiv.html("");
        } else {

            countertotal.show();
            totaldiv.html(total);
            if (total === 1) {
                textdiv.html("Filtro ativo");
            } else {
                textdiv.html("Filtros ativos");
            }
        }
        this.togglefilterbar.setAttribute("notifyCount", total);
    };

    this.setFiltersDisabled = function (value) {
        var listfilters = ctrl.filterCheckboxes;
        for (var key in listfilters) {
            var listinput = listfilters[key];
            for (var keyinput in listinput) {
                var input = listinput[keyinput];
                input.disabled = value;
            }
        }
        ctrl.autoupdateCheckbox.disabled = value;
        ctrl.resetBtn.disabled = value;
    };

    this.disableFilters = function () {
        ctrl.setFiltersDisabled(true);
    };

    this.enableFilters = function () {
        ctrl.setFiltersDisabled(false);
    };

    this.updateRequest = function () {
        if (ctrl.autoupdateCheckbox.checked) {
            var filters = ctrl.getFilters();
            cUI.mapCtrl.requestUpdate(filters);
        }
    };

    this.show = function () {
        if (ctrl.visiblefilters === false) {
            ctrl.visiblefilters = true;
            ctrl.filterbar.slideDown(200);
            ctrl.togglefilterbar.html('<i class="fa fa-angle-double-up"></i>');
            ctrl.togglefilterbar.toggleClass("active");
        }
    };

    this.close = function () {
        if (ctrl.visiblefilters) {
            ctrl.visiblefilters = false;
            ctrl.filterbar.slideUp(200);
            ctrl.togglefilterbar.html('<i class="fa fa-filter"></i>');
            ctrl.togglefilterbar.toggleClass("active");
        }
    }

    this.fadeOut = function (time) {
        ctrl.filterbar.slideUp(time);
        $(".ui-dialog .body").dialog("close");
        ctrl.togglefilterbar.toggleSlideVeritical(time);
        ctrl.visiblefilters = false;
        ctrl.togglefilterbar.html('<i class="fa fa-filter"></i>');
        ctrl.togglefilterbar.classList.remove("active");
    };

    this.toggle = function () {
        ctrl.filterbar.toggleSlideVeritical(200);
        ctrl.visiblefilters = !ctrl.visiblefilters;
        ctrl.togglefilterbar.toggleClass("active");
        if (ctrl.visiblefilters) {
            cUI.markerDialogCtrl.close();
            ctrl.togglefilterbar.html('<i class="fa fa-angle-double-up"></i>');
        } else {
            ctrl.togglefilterbar.html('<i class="fa fa-filter"></i>');
        }
        cUI.mapCtrl.DesabilitarModoInstituicao();
    };

    this.resetFilters = function (update) {
        if (update === undefined) {
            update = true;
        }
        var listfilters = ctrl.filterCheckboxes;
        for (var key in listfilters) {
            var listinput = listfilters[key];
            for (var keyinput in listinput) {
                var input = listinput[keyinput];
                input.checked = true;
            }

        }
        var listfilters = cUI.catchElement("filter-list").childlist(".filter-type");
        for (var key in listfilters) {
            var selectOne = listfilters[key].child(".select-one");
            listfilters[key].classList.remove("enabled");
            if (selectOne.checked) {
                selectOne.checked = false;
                selectOne.toggleClass("checked");
            }
        }
        if (update) {
            ctrl.updateFiltersActived();
            ctrl.updateRequest();
        }
    };

    this.getFilters = function () {
        var data = {};
        var listfilters = ctrl.filterCheckboxes;
        for (var key in listfilters) {
            data[key] = [];
            var listinput = listfilters[key];
            var all = true;
            for (var keyinput in listinput) {
                var input = listinput[keyinput];
                if (input.checked) {
                    data[key].push(input.value);
                } else {
                    all = false;
                }
            }
            if (all) {
                data[key] = {all: true};
            } else {
                data[key].all = false;
            }
        }
        return data;
    };

    this.setFilters = function (filters) {
        ctrl.resetFilters(false);

        for (var key in filters) {
            if (!filters[key].all) {
                var list = filters[key];
                var listcheckboxes = ctrl.filterCheckboxes[key];
                for (var keycheckbox in listcheckboxes) {
                    if (!list.includes(listcheckboxes[keycheckbox].value)) {
                        listcheckboxes[keycheckbox].checked = false;
                    }
                }
                ctrl.filterbar.child(".filter-type[name='" + key + "']").classList.add("enabled");
            } else {
                ctrl.filterbar.child(".filter-type[name='" + key + "']").classList.remove("enabled");
            }
        }

    };

    this.hide = function () {
        ctrl.filterbar.hide();
    };

    this.togglefilterbar.click(this.toggle);
    this.resetBtn.click(this.resetFilters);
    this.autoupdateCheckbox.change(this.updateRequest);
    this.hide();

    this.filterBtnEvent = function (event, data) {
        var lista = data.list;
        cUI.markerDialogCtrl.close();
        if (data.selectOne.checked) {
            for (var i = 0; i < lista.length; i++) {
                lista[i].checked = false;
            }
            data.source.checked = true;
            ctrl.updateRequest();
        } else {
            var found = false;
            var all = true;
            for (var i = 0; i < lista.length; i++) {
                var input = lista[i];
                if (input.checked) {
                    found = true;
                } else {
                    all = false;
                }
            }

            if (!found) {
                event.preventDefault();
                swal({type: "info", text: "É necessário pelo menos um dos itens selecionado"});
            } else {
                ctrl.updateRequest();
                if (all) {
                    data.filtertype.classList.remove("enabled");
                } else {
                    data.filtertype.classList.add("enabled");
                }
            }
        }
        ctrl.updateFiltersActived();
    };

    this.selectAllBtnEvent = function (event, data) {
        var list = data.list;
        cUI.markerDialogCtrl.close();
        for (var keyinput in list) {
            list[keyinput].checked = true;
        }
        if (data.selectOne.checked) {
            data.selectOne.checked = false;
            data.selectOne.toggleClass("checked");
        }
        data.filtertype.classList.remove("enabled");
        ctrl.updateRequest();
        ctrl.updateFiltersActived();
    };

    this.selectOneBtnEvent = function (event, data) {
        var btn = data.btn;
        var list = data.list;
        cUI.markerDialogCtrl.close();
        if (!data.selectOne.checked) {

            var total = 0;

            for (var keyinput in list) {
                if (list[keyinput].checked) {
                    total++;
                }
                if (total !== 1) {
                    list[keyinput].checked = false;
                }
            }
            data.filtertype.classList.add("enabled");
        }
        data.selectOne.checked = !data.selectOne.checked;
        btn.toggleClass("checked");
        ctrl.updateRequest();
        ctrl.updateFiltersActived();
    };

    this.toWindowBtnEvent = function (event, data) {
        var filtertype = data.filtertype;
        var body = filtertype.child(".body");
        var placeholder = $("<a/>").addClass("placeholder").html("Restaurar");
        var onclose = function (event, ui) {
            placeholder.remove();
            $(body).dialog("destroy");
            filtertype.append(body);
            filtertype.child(".to-window-btn.fa-window-maximize").show();
            filtertype.child(".to-window-btn.fa-reply").hide();
        };

        $(body).dialog({
            title: filtertype.child(".title").cText(),
            close: onclose,
            maxHeight: 600,
            position: {my: "right-10 top+10", at: "right-10 top+10", of: window}
        });

        $(".ui-button.ui-corner-all.ui-widget.ui-button-icon-only.ui-dialog-titlebar-close").html("<i class='fa fa-reply'></i>").css("text-indent", "0");
        filtertype.child(".to-window-btn.fa-window-maximize").hide();
        filtertype.child(".to-window-btn.fa-reply").show().removeAllClickEvents().click(onclose);

        placeholder.click(onclose).appendTo(filtertype);
    };

    var listfilters = cUI.catchElement("filter-list").childlist(".filter-type");

    for (var key in listfilters) {
        var value = listfilters[key].getAttribute("name");
        var selectAll = listfilters[key].child(".select-all");
        var selectOne = listfilters[key].child(".select-one");
        var towindowbtn = listfilters[key].child(".to-window-btn.fa-window-maximize");

        towindowbtn.click(this.toWindowBtnEvent, {filtertype: listfilters[key]});

        selectOne.checked = false;
        var listinput = listfilters[key].childlist("input");
        this.filterCheckboxes[value] = listinput;
        selectAll.click(this.selectAllBtnEvent, {list: listinput, selectOne: selectOne, filtertype: listfilters[key]});
        selectOne.click(this.selectOneBtnEvent, {list: listinput, btn: selectOne, selectOne: selectOne, filtertype: listfilters[key]});
        for (var keyinput in listinput) {
            var input = listinput[keyinput];
            input.click(this.filterBtnEvent, {source: input, selectOne: selectOne, filtertype: listfilters[key], list: listinput});
        }
    }

    var listtabs = this.filterbar.childlist(".filterbar-tab-header");
    var listfilterlists = this.filterbar.childlist(".filter-list");

    function selectTab(event, data) {
        for (var i = 0; i < listtabs.length; i++) {
            listtabs[i].classList.remove("selected");
            listfilterlists[i].hide();
        }
        listtabs[data.index].classList.add("selected");
        listfilterlists[data.index].show();
    }

    for (var i = 0; i < listtabs.length; i++) {
        listtabs[i].click(selectTab, {index: i});
    }

    $(".filter-list").sortable({
        items: "> li",
        handle: ".fa-ellipsis-v.draggable-sortable-btn"
    });

    $(".filterbar-tab-header").tipsy({gravity: "w"});

}