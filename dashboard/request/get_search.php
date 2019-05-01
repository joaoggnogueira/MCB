<?php

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("table", "term"))) {
    $table = $request->takePOST("table", RequestController::$PROCESS_STRING);
    $term = $request->takePOST("term", RequestController::$PROCESS_STRING);

    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
    $data = false;
    switch($table){
        case "programa": $data = $model->search_programa($term); break;
        case "local_de_oferta": $data = $model->search_local_oferta($term); break;
        case "instituicao": $data = $model->search_instituicao($term); break;
        case "municipio": $data = $model->search_municipio($term); break;
        case "mantenedora": $data = $model->search_mantanedora($term); break;
        default: $request->responseError("Tabela desconhecida: $table", ""); return;
    }
    if (is_array($data)) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError("Parametros incorretos", "Parametros incorretos");
}