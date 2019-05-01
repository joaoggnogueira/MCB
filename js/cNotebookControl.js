'use strict';
function cNotebookControl(notebook_elem, select_index = 0) { //classname = notebook

    notebook_elem = cUI.catchElement(notebook_elem);
    const ctrl = this;

    this.tabheaderlist = notebook_elem.childlist(".tab-header");
    this.tabslist = notebook_elem.childlist(".tab");

    this.tabheaderclickevent = function (event, selected) {
        for (let i = 0; i < ctrl.tabslist.length; i++) {
            ctrl.tabslist[i].classList.remove("selected");
            ctrl.tabheaderlist[i].classList.remove("selected");
        }
        ctrl.tabheaderlist[selected].classList.add("selected");
        ctrl.tabslist[selected].classList.add("selected");
    };

    for (let i = 0; i < this.tabheaderlist.length; i++) {
        this.tabheaderlist[i].click(this.tabheaderclickevent, i);
    }
    this.tabheaderlist[select_index].classList.add("selected");
    this.tabslist[select_index].classList.add("selected");
    
}