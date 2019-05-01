<?php

function replace_variables($string, $hash) {
    $saida = "";
    $len = strlen($string);
    for ($i = 0; $i < $len; $i++) {
        if ($string[$i] === "%") {
            $i++;
            if($i < $len) {
                $token = "";
                while ($string[$i] !== "%" && $i < $len) {
                    $token .= $string[$i];
                    $i++;
                }
                if (isset($hash[$token])) {
                    $saida .= $hash[$token];
                } else {
                    return ""; //error
                }
            } else {
                $saida .= "%";
            }
        } else {
            $saida .= $string[$i];
        }
        $i++;
    }
    return $saida;
}

function convert_data_campo($model, $mapaId, $json_campo, $func, $spec = false) {
    if ($json_campo->novo) {
        $rec = $model->get_campo_novo($json_campo->value);
        $data = (array) json_decode($rec['data']);
        if ($spec) {
            $value = replace_variables($spec, $data);
        } else {
            $value = $data[0];
        }
        return array("value" => $value, "novo" => true);
    } else {
        if ($json_campo->value != -1) {
            $rec = $model->{$func}($json_campo->value);
            $mapa = $mapaId;
            if (isset($rec['mapa'])) {
                $mapa = $rec['mapa'];
            }
            return array("value" => $rec['nome'], "novo" => false, "mapa" => $mapa);
        }
    }
    return array("value" => null, "novo" => false, "mapa" => null);
}

function convert_data($json, $model, $mapaId) {
    return array(
        "grau" => convert_data_campo($model, $mapaId, $json->grau, "get_grau"),
        "modalidade" => convert_data_campo($model, $mapaId, $json->modalidade, "get_modalidade"),
        "nivel" => convert_data_campo($model, $mapaId, $json->nivel, "get_nivel"),
        "programa" => convert_data_campo($model, $mapaId, $json->programa, "get_programa", "%display%"),
        "local_oferta" => convert_data_campo($model, $mapaId, $json->local_de_oferta, "get_local_oferta"),
        "ies" => convert_data_campo($model, $mapaId, $json->ies, "get_instituicao", "%sigla% - %nome%"),
        "tipo_organizacao" => convert_data_campo($model, $mapaId, $json->tipo_organizacao, "get_tipo_organizacao"),
        "municipio" => convert_data_campo($model, $mapaId, $json->municipio, "get_municipio"),
        "rede" => convert_data_campo($model, $mapaId, $json->rede, "get_rede"),
        "natureza" => convert_data_campo($model, $mapaId, $json->natureza, "get_natureza"),
        "naturezaJuridica" => convert_data_campo($model, $mapaId, $json->naturezaJuridica, "get_naturezaJuridica"),
        "mantenedora" => convert_data_campo($model, $mapaId, $json->mantenedora, "get_naturezaJuridica", "%nome%"),
        "matutino" => $json->matutino,
        "vespertino" => $json->vespertino,
        "noturno" => $json->noturno,
        "integral" => $json->integral,
        "total_de_alunos" => $json->total_de_alunos,
        "carga_horaria" => $json->carga_horaria,
        "adicional" => (isset($json->adicional)?$json->adicional:false)
    );
}

include "../../config/config.php";
include "../../controllers/RequestController.php";

$request = new RequestController();
$request->requestPayload();
if ($request->verifyPOST(array("id"))) {
    $id = $request->takePOST("id", RequestController::$PROCESS_INT);

    include "../../controllers/DatabaseController.php";
    include "../../models/DashboardModel.php";

    $model = new DashboardModel();
    $data = array();

    $edicao = $model->get_data_edicao($id);
    $prevedicao = false;

    $data['id_curso'] = $edicao['id_curso'];
    $data['mapa'] = $edicao['mapa'];
    $data['mapaId'] = $edicao['mapaId'];
    $data['nome'] = $edicao['nome'];
    $data['data_criacao'] = $edicao['data_criacao'];
    $data['hora_criacao'] = $edicao['hora_criacao'];

    if ($edicao['prev'] == 0) {
        //tratar caso existir uma sugestao com status F
        $prevedicao = $model->get_data_curso($edicao['id_curso']);
        $data['first_edicao'] = true;
    } else {
        $prevedicao = $model->get_data_edicao($edicao['prev']);
        $data['first_edicao'] = false;
    }

    $data['atual_edicao'] = convert_data(json_decode($edicao['data']), $model, $data['mapaId']);
    $data['prev_edicao'] = $prevedicao;

    if (is_array($data)) {
        $request->responseSuccess("Sucesso ao recuperar dados", $data);
    } else {
        $request->responseError("Erro ao retornar dados do banco", "");
    }
} else {
    $request->responseError("Parametros incorretos", "Parametros incorretos");
}