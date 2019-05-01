'use strict';
function cFilterControl() {

    this.filterbar = cUI.catchElement("filterbar");
    this.togglefilterbar = cUI.catchElement("btn-toggle-filter");
    this.visiblefilters = false;
    this.filterCheckboxes = [];
    this.counterFilters = cUI.catchElement("counter-filters");

    this.counterFilters.hide();

    const ctrl = this;

    this.updateFiltersActived = function () {
        const listfilters = ctrl.filterbar.childlist(".filter-type.enabled");
        const total = listfilters.length;
        const countertotal = ctrl.counterFilters;
        const totaldiv = countertotal.child(".total");
        const textdiv = countertotal.child(".text");

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
        const listfilters = ctrl.filterCheckboxes;
        for (let key in listfilters) {
            const listinput = listfilters[key];
            for (let keyinput in listinput) {
                const input = listinput[keyinput];
                input.disabled = value;
            }
        }
    };

    this.disableFilters = function () {
        ctrl.setFiltersDisabled(true);
    };

    this.enableFilters = function () {
        ctrl.setFiltersDisabled(false);
    };

    this.updateRequest = function () {
        const filters = ctrl.getFilters();
        cUI.mapCtrl.requestUpdate(filters);
    };

    this.show = function () {
        if (ctrl.visiblefilters === false) {
            $(ctrl.filterbar).css("opacity", 1);
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
    };

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
        let listfilters = ctrl.filterCheckboxes;
        for (let key in listfilters) {
            const listinput = listfilters[key];
            for (let keyinput in listinput) {
                const input = listinput[keyinput];
                input.checked = true;
            }

        }
        listfilters = cUI.catchElement("filter-list").childlist(".filter-type");
        for (let key in listfilters) {
            const selectOne = listfilters[key].child(".select-one");
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
        const data = {};
        const listfilters = ctrl.filterCheckboxes;
        for (let key in listfilters) {
            data[key] = [];
            const listinput = listfilters[key];
            let all = true;
            for (let keyinput in listinput) {
                const input = listinput[keyinput];
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

        for (let key in filters) {
            if (!filters[key].all) {
                const list = filters[key];
                const listcheckboxes = ctrl.filterCheckboxes[key];
                for (let keycheckbox in listcheckboxes) {
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
    this.hide();

    this.filterBtnEvent = function (event, data) {
        const lista = data.list;
        cUI.markerDialogCtrl.close();
        if (data.selectOne.checked) {
            for (let i = 0; i < lista.length; i++) {
                lista[i].checked = false;
            }
            data.source.checked = true;
            ctrl.updateRequest();
        } else {
            let found = false;
            let all = true;
            for (let i = 0; i < lista.length; i++) {
                const input = lista[i];
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
        const list = data.list;
        cUI.markerDialogCtrl.close();
        for (let keyinput in list) {
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
        const btn = data.btn;
        const list = data.list;
        cUI.markerDialogCtrl.close();
        if (!data.selectOne.checked) {

            let total = 0;

            for (let keyinput in list) {
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
        const filtertype = data.filtertype;
        const body = filtertype.child(".body");
        const placeholder = $("<a/>").addClass("placeholder").html("Restaurar");
        const onclose = function (event, ui) {
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

    const listfilters = cUI.catchElement("filter-list").childlist(".filter-type");

    listfilters.forEach((list) => {
        const value = list.getAttribute("name");
        const selectAll = list.child(".select-all");
        const selectOne = list.child(".select-one");
        const towindowbtn = list.child(".to-window-btn.fa-window-maximize");

        towindowbtn.click(this.toWindowBtnEvent, {filtertype: list});

        selectOne.checked = false;
        const listinput = list.childlist("input");
        this.filterCheckboxes[value] = listinput;
        selectAll.click(this.selectAllBtnEvent, {list: listinput, selectOne: selectOne, filtertype: list});
        selectOne.click(this.selectOneBtnEvent, {list: listinput, btn: selectOne, selectOne: selectOne, filtertype: list});
        listinput.forEach((input) => {
            input.click(this.filterBtnEvent, {source: input, selectOne: selectOne, filtertype: list, list: listinput});
        });
    });

    const listtabs = this.filterbar.childlist(".filterbar-tab-header");
    const listfilterlists = this.filterbar.childlist(".filter-list");

    function selectTab(event, data) {
        for (let i = 0; i < listtabs.length; i++) {
            listtabs[i].classList.remove("selected");
            listfilterlists[i].hide();
        }
        listtabs[data.index].classList.add("selected");
        listfilterlists[data.index].show();
    }
    listtabs.forEach((tab, i) => {
        tab.click(selectTab, {index: i});
    });

    $(".filter-list").sortable({
        items: "> li",
        handle: ".fa-ellipsis-v.draggable-sortable-btn"
    });

    $(".filterbar-tab-header").tipsy({gravity: "w"});

}