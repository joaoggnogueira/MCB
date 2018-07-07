<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id"])) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";

    $model = new RelatorioModel();
    $data = $model->getCursoDetails($id);

    if ($data) {
        ob_start();
        include './htmlappend/cursodetails.php';
        $view = ob_get_clean();
        ob_end_flush();
        $request->responseSuccess("Sucesso ao recuperar dados", $view);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError($request->getErrors(), "");
}