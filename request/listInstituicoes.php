<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();
    
include "../controllers/DatabaseController.php";
include "../models/RelatorioModel.php";
$model = new RelatorioModel();
$result = $model->listInstituicoes();
if ($result) {
    ob_start("ob_gzhandler");
    $request->responseSuccess("Sucesso ao recuperar dados", $result);
} else {
    $request->responseError("Erro ao retornar dados do banco", "");
}