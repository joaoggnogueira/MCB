
//define uma interface customizada entre jquery e a aplicação    

(function () {

    window.cUI = new function () {

        this.sidebarCtrl = null;
        this.filterCtrl = null;
        this.mapCtrl = null;
        this.markerDialogCtrl = null;
        this.body = null;

        this.createMarkerDialogCtrl = function(){
            this.markerDialogCtrl = new cMarkerDialogControl();
        };

        this.createMapCtrl = function(){
            this.mapCtrl = new cMapControl();
        };

        this.createBodyCtrl = function () {
            this.body = this.catchElement(document.body);
            this.body.setStates(["blue-theme", "purple-theme", "green-yellow-theme", "red-theme", "orange-theme", "cyan-theme", "pink-theme"]);
        };

        this.createFilters = function () {
            this.filterCtrl = new cFilterControl();
        };

        this.createSidebar = function () {
            this.sidebarCtrl = new cSidebarControl();
        };

        this.catchElement = function (elem) {
            var d;

            if (elem instanceof HTMLElement) {
                d = elem;
            } else {
                d = document.getElementById(elem);

                if (d === null || d === undefined) {
                    alert(elem + " não encontrado");
                }
            }
            if (d.cInterfaceInitialized) {
                return d;
            }
            d.append = function(child){
                $(d).append(child);
            }
            d.removeAllClickEvents = function(){
                $(d).off("click");
                return d;
            }
            d.setStates = function (arr) {
                var item;
                if (d.id.length !== 0) {
                    item = localStorage.getItem("mcomputa_" + d.id);
                    if (item) {
                        d.atualstate = parseInt(item);
                    } else {
                        d.atualstate = 0;
                    }
                } else {
                    d.atualstate = 0;
                }
                d.states = arr;
                d.classList.add(arr[d.atualstate]);
            };
            d.nextState = function () {
                d.classList.remove(d.states[d.atualstate]);
                d.atualstate = (d.atualstate + 1) % d.states.length;
                d.classList.add(d.states[d.atualstate]);
                if (d.id.length !== 0) {
                    var item_key = "mcomputa_" + d.id;
                    localStorage.setItem(item_key, d.atualstate);
                }
            };
            d.childlist = function (query) {
                var querylist = $(d).find(query);
                var list = [];
                for(var i=0;i<querylist.length;i++){
                    list.push(cUI.catchElement(querylist[i]));
                }
                return list;
            };
            d.child = function (query) {
                return cUI.catchElement($(d).find(query)[0]);
            };
            d.change = function (callback,data) {
                d.addEventListener("change", function(event){
                    callback(event,data);
                });
                return d;
            };
            d.disable = function(){
                d.setAttribute("disabled","disabled");
                return d;
            };
            d.enable = function(){
                d.removeAttribute("disabled");
                return d;
            }
            d.click = function (callback,data) {
                if(callback){
                    d.addEventListener("click", function(event){
                        callback(event,data);
                    });
                    return d;
                } else {
                    $(d).click();
                }
            };
            d.fadeIn = function(time,callback){
                $(d).fadeIn(time, "easeInOutQuint",callback);
            };
            d.fadeOut = function(time,callback) {
                $(d).fadeOut(time, "easeInOutQuint",callback);
            };
            d.hide = function () {
                $(d).hide();
                return d;
            };
            d.show = function(){
                $(d).show();
                return d;
            };
            d.html = function (html) {
                if(html === undefined){
                    return $(d).html();
                } else {
                    $(d).html(html);
                    return d;
                }
            };
            d.cText = function(text){
                if(text === undefined){
                    return $(d).text();
                } else {
                    $(d).text(text);
                    return d;
                }
            };
            d.slideDown = function (time) {
                $(d).slideDown(time, "easeInOutQuint");
                return d;
            };
            d.slideUp = function (time) {
                $(d).slideUp(time, "easeInOutQuint");
                return d;
            };
            d.toggleSlideVeritical = function (time) {
                $(d).slideToggle(time, "easeInOutQuint");
                return d;
            };
            d.toggleSlideHorizontal = function (time) {
                $(d).animate({width: 'toggle'}, time, "easeInOutQuint");
                return d;
            };
            d.toggleFade = function (time) {
                $(d).fadeToggle(time, "easeInOutQuint");
                return d;
            };
            d.toggleClass = function (className) {
                d.classList.toggle(className);
                return d;
            };
            d.cInterfaceInitialized = true;

            return d;
        };

    };

})();