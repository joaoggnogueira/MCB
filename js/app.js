function initMap() {
    cUI.createBodyCtrl();
    cUI.createMapCtrl();
    cUI.createSidebar();
    cUI.createFilters();
    cUI.createMarkerDialogCtrl();
    
    $("#loading").remove();
    
    cUI.mapCtrl.appendLeft(cUI.sidebarCtrl.theater);
    cUI.mapCtrl.appendLeft(cUI.sidebarCtrl.sidebar);
    cUI.mapCtrl.appendLeft(cUI.filterCtrl.togglefilterbar);
    cUI.mapCtrl.appendLeft(cUI.filterCtrl.filterbar);
    cUI.mapCtrl.appendLeft(cUI.sidebarCtrl.sidebarBtn);
    cUI.mapCtrl.appendRight(cUI.markerDialogCtrl.dialog);
    cUI.mapCtrl.appendRight(cUI.markerDialogCtrl.theater);
    
    onload_all();

}