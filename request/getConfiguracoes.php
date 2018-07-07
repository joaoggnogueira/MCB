<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id"])) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";

    $model = new RelatorioModel();
    $result = $model->getConfiguracoes($id);

    if ($result) {
        $request->responseSuccess("Sucesso ao recuperar dados", $result);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError($request->getErrors(), "");
}