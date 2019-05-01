<?php


include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("id_mapa"))) {
    $id_mapa = $request->takePOST("id_mapa", RequestController::$PROCESS_INT);
    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";
    
    $model = new DashboardModel();
    $data = array();
    $data['grau'] = array_merge($model->list_grau(), $model->list_grau_campo_novo());
    $data['modalidade'] = $model->list_modalidade($id_mapa);
    $data['nivel'] = $model->list_nivel($id_mapa);
    $data['rede'] = $model->list_rede();
    $data['natureza'] = $model->list_natureza();
    $data['natureza_juridica'] = $model->list_natureza_juridica();
    $data['tipo_organizacao'] =  array_merge($model->list_tipo_organizacao(), $model->list_tipo_organizacao_campo_novo());
    
    $request->responseSuccess("Sucesso ao recuperar dados", $data);
} else {
    $request->responseError("Parametros incorretos", "Parametros incorretos");
}
