<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["cod_mun","filters"])) {
    $cod_mun = $request->takePOST("cod_mun", RequestController::$PROCESS_INT);
    $filters = $request->takePOST("filters", RequestController::$PROCESS_JSON);
    
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";
    $model = new RelatorioModel();
    $result = $model->listCursos($cod_mun,$filters);
    if ($result) {
        ob_start("ob_gzhandler");
        $request->responseSuccess("Sucesso ao recuperar dados", $result);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError($request->getErrors(), "");
}