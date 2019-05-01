<?php

session_start();

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if (isset($_SESSION['google_profile'])) {
    $google_profile = json_decode($_SESSION['google_profile']);

    if ($request->verifyPOST(array("id_curso", "id_usergoogle", "id_mapa", "data", "novo_data"))) {
        $id_curso = $request->takePOST("id_curso", RequestController::$PROCESS_INT);
        $id_usergoogle = $request->takePOST("id_usergoogle", RequestController::$PROCESS_STRING);
        $id_mapa = $request->takePOST("id_mapa", RequestController::$PROCESS_INT);
        $data = $request->takePOST("data");
        $novo_data = (array) $request->takePOST("novo_data");
        $reuse_in = (array) $request->takePOST("reuse_in");

        if ($google_profile->id == $id_usergoogle) {
            include "../../controllers/DatabaseController.php";
            include "../../models/DashboardModel.php";

            $model = new DashboardModel();
            try {
                $model->beginTransaction();

                $prev = $model->get_atual_revisao($id_curso);
                $json_data = array();

                foreach ($data as $tabela => $campo) {
                    $value = $campo;
                    $novo = false;

                    if ($value === "novo") {
                        $dnovo = $novo_data[$tabela];
                        $display = "";

                        switch ($tabela) {
                            case "grau": 
                                $display = $dnovo;
                                break;
                            case "local_de_oferta":
                                $display = $dnovo;
                                break;
                            case "tipo_organizacao": 
                                $display = $dnovo;
                                break;
                            case "programa":
                                $display = $dnovo->cod . ' - ' . $dnovo->nome;
                                break;
                            case "mantenedora":
                                $display = $dnovo->nome;
                                break;
                        }

                        $value = $model->insert_sugestao_campo_novo($tabela, $id_mapa, $id_usergoogle, $dnovo, $display);
                        if ($value == false) {
                            $model->rollback();
                            $request->responseError("Falha na requisição", "Erro ao cadastrar novo campo em $tabela");
                            return;
                        }
                        $novo = true;
                    } else if (isset($reuse_in[$tabela])) {
                        $novo = true;
                    }

                    $json_data[$tabela] = array("novo" => $novo, "value" => $value);
                }
            } catch (Exception $e) {
                $model->rollback();
                $request->responseError("Falha na requisição", "Erro ao critico '$e'");
            }

            if ($model->insert_sugestao($id_curso, $id_usergoogle, $json_data, $prev)) {
                $model->commit();
                $request->responseSuccess("Sucesso ao cadastrar sugestão", "Sucesso");
            } else {
                $request->responseError("Falha na requisição", "Erro ao cadastrar curso");
            }
        } else {
            $request->responseError("ID do usuário não bate com o da sessão", "Parametros incorretos");
        }
    } else {
        $request->responseError("Parametros incorretos", "Parametros incorretos");
    }
} else {
    $request->responseError("Sem sessão iniciada", "Sem sessão iniciada");
}
