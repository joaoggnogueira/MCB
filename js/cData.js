
(function () {

    window.cData = new function () {


        this.saveConfiguracoes = function (rotulo, json) {
            cRequest.postJson("saveConfiguracoes.php", {rotulo: rotulo, json: JSON.stringify(json)}, function (data) {
                swal({type: "success", title: "Relatório Salvo", html: "Use o seguinte link para compartilhar:<br/><input type='text' style='width:350px;text-align:center;' value='" + ROOT_APP + "index.php?savedconfig=" + data.data + "'/>"});
            });
        };

        var request_markers = false;
        this.requestMarkers = function (filters, mapa, callback) {
            if (request_markers) {
                request_markers.abort();
            }
            request_markers = cRequest.postJson("requestMarkers.php", {filters: JSON.stringify(filters), mapa: mapa}, function (data) {
                request_markers = false;
                callback(data.data);
            });
        };

        this.listConfiguracoes = function (callback) {
            cRequest.postJson("listConfiguracoes.php", {}, function (data) {
                callback(data.data);
            });
        };

        this.getConfiguracoes = function (id, callback) {
            cRequest.postJson("getConfiguracoes.php", {id: id}, function (data) {
                callback(data.data);
            });
        };

        var request_cursos = false;
        this.listCursos = function (id, mapa, filters, callback) {
            if (request_cursos) {
                request_cursos.abort();
            }
            console.log(mapa);
            request_cursos = cRequest.postJson("listCursos.php", {id: id, mapa: mapa, filters: JSON.stringify(filters)}, function (data) {
                request_markers = false;
                callback(data.data);
            });
        };

        this.getCursoDetailsHTML = function (id, callback) {
            cRequest.postJson("getCursoDetailsHTML.php", {id: id}, function (data) {
                callback(data.data);
            });
        };

        this.getMunicipioDetailsHTML = function (id, markerType, callback) {
            cRequest.postJson("getMunicipioDetailsHTML.php", {id: id, markerType: markerType}, function (data) {
                callback(data.data);
            });
        };

        this.listInstituicoes = function (callback) {
            cRequest.postJson("listInstituicoes.php", {}, function (data) {
                callback(data.data);
            });
        };

        this.listAnoEnade = function (id_inst, cod_mun, callback) {
            cRequest.postJson("listEnadeAno.php", {id_inst: id_inst, cod_mun: cod_mun}, function (data) {
                callback(data.data);
            });
        };

        this.listAreaEnade = function (id_inst, cod_mun, ano, id_curso, callback) {
            cRequest.postJson("listEnadeArea.php", {id_curso: id_curso, id_inst: id_inst, ano: ano, cod_mun: cod_mun}, function (data) {
                callback(data.data);
            });
        };

        this.getAvaliacaoEnade = function (id_area, cod_mun, ano, id_inst, callback) {
            cRequest.postJson("getAvaliacao.php", {id_area: id_area, cod_mun: cod_mun, ano: ano, id_inst: id_inst}, function (data) {
                callback(data.data);
            });
        };

        this.getTotais = function (cod, mapa, filters, markerType, table, callback) {
            cRequest.postJson("getTotais.php", {cod: cod, filters: JSON.stringify(filters), markerType: markerType, table: table, mapa: mapa}, function (data) {
                callback(data.data);
            });
        };

    };

})();
