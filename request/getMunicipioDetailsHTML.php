<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id", "markerType"])) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    $markerType = $request->takePOST("markerType", RequestController::$PROCESS_INT);
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";

    $model = new RelatorioModel();
    $data = $model->getMarkerDetails($id, $markerType);
    if ($data) {
        ob_start();
        include './htmlappend/markerdetails.php';
        $view = ob_get_clean();
        ob_end_flush();
        $request->responseSuccess("Sucesso ao recuperar dados", array("view" => ($view)));
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError($request->getErrors(), "");
}