<?php

include "../config/config.php";
include "../controllers/RequestController.php";

$request = new RequestController();

if ($request->verifyPOST(["rotulo","json"])) {
    $rotulo = $request->takePOST("rotulo");
    $json = $request->takePOST("json");
    
    include "../controllers/DatabaseController.php";
    include "../models/RelatorioModel.php";
    
    $model = new RelatorioModel();
    
    $model->beginTransaction();
    if($model->saveConfiguracoes($rotulo,$json)){
        $id = $model->getLastInsertedId();
        $model->commit();
        $request->responseSuccess("Sucesso ao salvar dados", $id);
    } else  {
        $request->responseError("Erro ao salvar dados do banco", $model->getLastquery());
    }
} else {
    $request->responseError($request->getErrors(), "");
}