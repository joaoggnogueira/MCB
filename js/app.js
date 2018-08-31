function initMap() {
    cUI.createBodyCtrl();
    cUI.createMapCtrl();
    cUI.createFilters();
    cUI.createMarkerDialogCtrl();
    
    $("#loading").remove();
    
    cUI.mapCtrl.appendLeft(cUI.filterCtrl.togglefilterbar);
    cUI.mapCtrl.appendLeft(cUI.filterCtrl.filterbar);
    cUI.mapCtrl.appendRight(cUI.markerDialogCtrl.dialog);
    cUI.mapCtrl.appendRight(cUI.markerDialogCtrl.theater);
    
    onload_all();
    $("#visual-selected-text").selectmenu();
    $("#marker-selected-text").selectmenu();

}