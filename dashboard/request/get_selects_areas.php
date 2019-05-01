<?php

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();

include "../../controllers/DatabaseController.php";
include "../../models/DashboardModel.php";

$model = new DashboardModel();
$data = array();
$data['area_geral'] = $model->list_area_geral();
$data['area_especifica'] = $model->list_area_especifica();
$data['area_detalhada'] = $model->list_area_detalhada();

$request->responseSuccess("Sucesso ao recuperar dados", $data);