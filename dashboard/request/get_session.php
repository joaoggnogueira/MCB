<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if (isset($_SESSION['google_profile'])) {
    $google_profile = json_decode($_SESSION['google_profile']);
    if (isset($_SESSION['google_profile'])) {
        $admin = check_if_are_admin($google_profile->id, $google_profile->email);
        $data = array("data" => $google_profile, "admin" => $admin);
        $request->responseSuccess("Sucesso ao salvar sessão", $data);
    } else {
        $request->responseError("Não foi possível obter a sessão", "Sessão não foi iniciada");
    }
} else {
    $request->responseError("Sem sessão iniciada", "Sem sessão iniciada");
}