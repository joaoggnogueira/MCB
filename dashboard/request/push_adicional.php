<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();

if ($request->verifyPOST(array("id", "password", "contents"))) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    $password = $request->takePOST("password", RequestController::$PROCESS_STRING);
    $contents = $request->takePOST("contents", RequestController::$PROCESS_STRING);
    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
        
    if ($model->push_adicional($id, $password, $contents)) {
        $request->responseSuccess("Sucesso ao cadastrar", "Sucesso");
    } else {
        $request->responseError("Falha na requisição", "Erro ao cadastrar curso");
    }
} else {
    $request->responseError("Parametros incorretos", "Parametros incorretos");
}
