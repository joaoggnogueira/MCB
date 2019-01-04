<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();

if ($request->verifyPOST(["filters"])) {
    $filters = $request->takePOST("filters", RequestController::$PROCESS_JSON);
    $mapa = $request->takePOST("mapa", RequestController::$PROCESS_JSON);
    
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";
    
    $model = new RelatorioModel();
    $result = false;
    switch($filters->markerType){
        case 0:
            $result = $model->listMarkersMunicipios($filters, $mapa);
            break;
        case 1:
            $result = $model->listMarkersEstado($filters, $mapa);
            break;
        case 2:
            $result = $model->listMarkersRegiao($filters, $mapa);
            break;
    }
    if(is_array($result)){
        ob_start("ob_gzhandler");
        $request->responseSuccess("Sucesso ao recuperar dados", $result);
    } else  {
        echo $model->getLastquery();
        $request->responseError("Erro ao retornar dados do banco", $model->getLog());
    }
} else {
    $request->responseError($request->getErrors(), "");
}