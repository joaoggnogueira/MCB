<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if (isset($_SESSION['google_profile'])) {
    unset($_SESSION['google_profile']);
    $request->responseSuccess("Sucesso ao encerrar sessão", array());
} else {
    $request->responseError("Sem sessão iniciada", "Sem sessão iniciada");
}