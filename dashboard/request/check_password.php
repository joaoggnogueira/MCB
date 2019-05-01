<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("password","id"))) {
    $password = $request->takePOST("password", RequestController::$PROCESS_STRING);
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    
    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
    $data = $model->checkpassword_curso($id, $password);
    if ($data) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError("Parametros incorretos ", "Parametros incorretos");
}