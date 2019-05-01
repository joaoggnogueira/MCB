<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();

if (isset($_SESSION['google_profile'])) {
    $google_profile = json_decode($_SESSION['google_profile']);

    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
    $data = $model->list_sugestoes_public($google_profile->id);
    if (is_array($data)) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Falha no Banco", "Falha no Banco");
    }
} else {
    $request->responseError("Sessão não iniciada", "Sessão não iniciada");
}