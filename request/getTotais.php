<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();

if ($request->verifyPOST(["filters"])) {
    $filters = $request->takePOST("filters", RequestController::$PROCESS_JSON);
    $cod = $request->takePOST("cod", RequestController::$PROCESS_INT);
    $markerType = $request->takePOST("markerType", RequestController::$PROCESS_INT);
    $table = $request->takePOST("table", RequestController::$PROCESS_STRING);
    $mapa = $request->takePOST("mapa", RequestController::$PROCESS_INT);
    
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";
    
    $model = new RelatorioModel();
    $totais = $model->totais($table, $cod, $filters, $markerType, $mapa);
    if(is_array($totais)){
        ob_start("ob_gzhandler");
        $request->responseSuccess("Sucesso ao recuperar dados", $totais);
    } else  {
        echo $model->getLastquery();
        $request->responseError("Erro ao retornar dados do banco", $model->getLog());
    }
} else {
    $request->responseError($request->getErrors(), "");
}