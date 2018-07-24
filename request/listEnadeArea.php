<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id_inst", "cod_mun", "ano", "id_curso"])) {
    $id_inst = $request->takePOST("id_inst", RequestController::$PROCESS_INT);
    $cod_mun = $request->takePOST("cod_mun", RequestController::$PROCESS_INT);
    $ano = $request->takePOST("ano", RequestController::$PROCESS_INT);
    $id_curso = $request->takePOST("id_curso", RequestController::$PROCESS_INT);
    include "../controllers/DatabaseController.php";
    include "../models/EnadeModel.php";

    $model = new EnadeModel();
    $data = $model->getAvaliacoesArea($id_inst, $cod_mun, $ano);
    $inducao = $model->inducaoAreaEnadeCurso($id_curso);
    if (is_array($data)) {
        $request->responseSuccess("Sucesso ao recuperar dados", array("list" => $data, "inducao" => $inducao));
    } else {
        $request->responseError("Erro ao retornar dados do banco", print_r($data, true));
    }
} else {
    $request->responseError($request->getErrors(), "");
}