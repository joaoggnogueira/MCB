<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id_inst", "cod_mun"])) {
    $id_inst = $request->takePOST("id_inst", RequestController::$PROCESS_INT);
    $cod_mun = $request->takePOST("cod_mun", RequestController::$PROCESS_INT);
    include "../controllers/DatabaseController.php";
    include "../models/EnadeModel.php";

    $model = new EnadeModel();
    $data = $model->getAvaliacoesAno($id_inst, $cod_mun);
    if (is_array($data)) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Erro ao retornar dados do banco",  print_r($data,true));
    }
} else {
    $request->responseError($request->getErrors(), "");
}