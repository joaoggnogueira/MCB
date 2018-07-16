<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id","filters"])) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    $filters = $request->takePOST("filters", RequestController::$PROCESS_JSON);
    
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";
    $model = new RelatorioModel();
    $result = false;
    switch($filters->markerType){
        case 0:
            $result = $model->listCursosByMunicipio($id,$filters);
            break;
        case 1:
            $result = $model->listCursosByEstado($id,$filters);
            break;
        case 2:
            $result = $model->listCursosByRegiao($id,$filters);
            break;
    }
    if ($result) {
        ob_start("ob_gzhandler");
        $request->responseSuccess("Sucesso ao recuperar dados", $result);
    } else {
        $request->responseError("Erro ao retornar dados do banco", array($result));
    }
} else {
    $request->responseError($request->getErrors(), "");
}