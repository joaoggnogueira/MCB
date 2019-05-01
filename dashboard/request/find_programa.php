<?php

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("nome","cod"))) {
    $nome = $request->takePOST("nome", RequestController::$PROCESS_STRING);
    $cod = $request->takePOST("cod", RequestController::$PROCESS_STRING);
    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
    $data = $model->find_programa($cod, $nome);
    if (is_array($data)) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError("Parametros incorretos " . print_r($data, true), "Parametros incorretos");
}