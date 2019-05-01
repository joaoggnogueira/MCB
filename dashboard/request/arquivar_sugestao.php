<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if (isset($_SESSION['google_profile'])) {
    $google_profile = json_decode($_SESSION['google_profile']);
    if ($request->verifyPOST(array("id"))) {
        $id = $request->takePOST("id", RequestController::$PROCESS_INT);
        include "../../controllers/DatabaseController.php";
        include "../../models/DashboardModel.php";

        $model = new DashboardModel();
        $data = $model->change_status_sugestao($id, $google_profile->id, "X");
        if ($data) {
            $request->responseSuccess("Sucesso ao recuperar dados", $data);
        } else {
            $request->responseError("Erro ao retornar dados do banco", "");
        }
    } else {
        $request->responseError("Parametros incorretos " . print_r($data, true), "Parametros incorretos");
    }
} else {
    $request->responseError("Sessão não iniciada", "Sessão não iniciada");
}