

function cNotebookControl(notebook_elem) { //classname = notebook

    notebook_elem = cUI.catchElement(notebook_elem);

    this.tabheaderlist = notebook_elem.childlist(".tab-header");
    this.tabslist = notebook_elem.childlist(".tab");

    this.tabheaderclickevent = function (event, selected) {
        for (var i = 0; i < ctrl.tabslist.length; i++) {
            ctrl.tabslist[i].classList.remove("selected");
            ctrl.tabheaderlist[i].classList.remove("selected");
        }
        ctrl.tabheaderlist[selected].classList.add("selected");
        ctrl.tabslist[selected].classList.add("selected");
    };

    for (var i = 0; i < this.tabheaderlist.length; i++) {
        this.tabheaderlist[i].click(this.tabheaderclickevent, i);
    }

    this.tabheaderlist[0].classList.add("selected");
    this.tabslist[0].classList.add("selected");

    var ctrl = this;
}