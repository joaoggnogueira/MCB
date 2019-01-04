<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
if ($request->verifyPOST(["id"])) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";
    include "../models/EnadeModel.php";

    $model = new RelatorioModel();
    $enadeModel = new EnadeModel();
    $data = $model->getCursoDetails($id);
    $data['conceito_enade_campus'] = $enadeModel->getAvaliacoesCampus($data['id_instituicao']);
    
    if ($data) {
        ob_start();
        include './htmlappend/cursodetails.php';
        $view = ob_get_clean();
        ob_end_flush();
        $request->responseSuccess(
                "Sucesso ao recuperar dados", 
                array("view" => utf8_encode($view),
                    "cod_mun" => $data['codigo_municipio'],
                    "nome_instituicao" => $data["nome_da_instituicao"], 
                    "sigla_instituicao" => $data["sigla_da_instituicao"], 
                    "id_inst" => $data['id_instituicao'])
        );
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError($request->getErrors(), "");
}