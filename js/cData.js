
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
        
        this.listCursos = function(cod_mun,filters,callback){
            cRequest.postJson("listCursos.php",{cod_mun:cod_mun,filters: JSON.stringify(filters)},function(data){
                callback(data.data);
            });
        };
        
        this.getDetailsHTML = function(id,callback){
            cRequest.postJson("getCursoDetailsHTML.php",{id:id},function(data){
                callback(data.data);
            });
        };
        
        
    };

})();
