
function cMarkerDialogControl() {

    this.dialog = cUI.catchElement("marker-dialog");
    this.datatable = null;
    this.copytable = null;
    this.close_btn = this.dialog.child(".close-btn");
    this.notebook = new cNotebookControl("notebook-marker-dialog");
    this.theater = cUI.catchElement("theater-details");
    var ctrl = this;
    
    this.showTheater = function(html){
        ctrl.theater.show();
        ctrl.theater.child(".modal-content").innerHTML = html;
    };
    
    this.closeTheater = function(){
        ctrl.theater.hide();
        ctrl.theater.child(".modal-content").innerHTML = "";
    };
    
    this.open = function (data) {
        console.log(data);
        ctrl.dialog.slideDown(400);
        cUI.catchElement("name-mun").html(data.name_mun);
        cUI.catchElement("cod-mun").html(data.cod_mun);
        if (ctrl.datatable) {
            ctrl.datatable.destroy(true);
            cUI.catchElement("cursos-tab").innerHTML = ctrl.copytable;
        } else {
            ctrl.copytable = cUI.catchElement("cursos-tab").innerHTML;
        }
        
        var list = [];
        for(var key in data.data){
            var row = data.data[key];
            list[key] = [];
            list[key][0] = row[0];
            list[key][1] = row[1];
            if(row[2].length !== 0){
                list[key][2] = row[2];
            } else {
                list[key][2] = row[3];
            }
        }
        
        ctrl.datatable = $("#table-cursos").DataTable({
            data: list,
            "columnDefs": [
                {"visible": false, "searchable": false, "targets": 0}
            ]
        });
        $('#table-cursos tbody').on('click', 'tr', function () {
            var row = ctrl.datatable.row(this).data();
            var id = row[0];
            cData.getDetailsHTML(id,function(html){
                ctrl.showTheater(html);
                new cNotebookControl("details-dialog");
                ctrl.theater.child(".btn-close").click(ctrl.closeTheater);
            });
        });
    };

    this.close = function () {
        ctrl.dialog.slideUp(400);
    };
    
    this.close_btn.click(this.close);
    this.dialog.hide();
    this.theater.hide();
}