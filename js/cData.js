
(function () {

    window.cData = new function () {
        
        this.saveConfiguracoes = function(rotulo,json){
            cRequest.postJson("saveConfiguracoes.php",{rotulo:rotulo,json:JSON.stringify(json)},function(data){
                swal({type:"success",title:"Relatório Salvo",html:"Use o seguinte link para compartilhar:<br/><input type='text' style='width:350px;text-align:center;' value='"+ROOT_APP+"index.php?savedconfig="+data.data+"'/>"});
            });
        };
        
        this.requestMarkers = function(filters,callback){
            cRequest.postJson("requestMarkers.php", {filters: JSON.stringify(filters)}, function (data) {
                callback(data.data);
            });
        };
        
        this.listConfiguracoes = function(callback){
            cRequest.postJson("listConfiguracoes.php",{},function(data){
                callback(data.data);
            });
        };
        
        this.getConfiguracoes = function(id,callback){
            cRequest.postJson("getConfiguracoes.php",{id:id},function(data){
                callback(data.data);
            });
        };
        
        this.listCursos = function(id,filters,callback){
            cRequest.postJson("listCursos.php",{id:id,filters: JSON.stringify(filters)},function(data){
                callback(data.data);
            });
        };
        
        this.getDetailsHTML = function(id,callback){
            cRequest.postJson("getCursoDetailsHTML.php",{id:id},function(data){
                callback(data.data);
            });
        };
        
        this.listInstituicoes = function(callback){
            cRequest.postJson("listInstituicoes.php",{},function(data){
                callback(data.data);
            });
        };
        
        
    };

})();
