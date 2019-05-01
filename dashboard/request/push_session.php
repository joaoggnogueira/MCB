<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("google_profile"))) {
    $google_profile = $request->takePOST("google_profile");

    $_SESSION['google_profile'] = json_encode($google_profile);

    $data = array("admin" => check_if_are_admin($google_profile->id, $google_profile->email));

    $request->responseSuccess("Sucesso ao salvar sessão", $data);
} else {
    $request->responseError("Parametros incorretos", "Parametros incorretos");
}