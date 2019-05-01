<?php

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("id","password"))) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    $password = $request->takePOST("password", RequestController::$PROCESS_STRING);
    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
    $data = $model->get_curso($id, $password);
    if ($data) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError("Parametros incorretos " . print_r($data, true), "Parametros incorretos");
}