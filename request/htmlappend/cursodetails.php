<?PHP
if (!isset($data)) {
    echo "Não é permitido acesso direto! 0x0001";
    exit();
}

$periodo_definido = $data['eh_matutino'] === '1' || $data['eh_vespertino'] === '1' || $data['eh_noturno'] === '1' || $data['eh_integral'] === '1';

function parser_data($data) {
    if (count($data) !== 0) {
        $dia = substr($data, 0, 2);
        $mes = substr($data, 2, 3);
        switch ($mes) {
            case "JAN":$mes = "JANEIRO";
                break;
            case "FEB":$mes = "FEVEREIRO";
                break;
            case "MAR":$mes = "MARÇO";
                break;
            case "APR":$mes = "ABRIL";
                break;
            case "MAY":$mes = "MAIO";
                break;
            case "JUN":$mes = "JUNHO";
                break;
            case "JUL":$mes = "JULHO";
                break;
            case "AUG":$mes = "AGOSTO";
                break;
            case "SEP":$mes = "SETEMBRO";
                break;
            case "OCT":$mes = "OUTUBRO";
                break;
            case "NOV":$mes = "NOVEMBRO";
                break;
            case "DEC":$mes = "DEZEMBRO";
                break;
            default: $mes = "???";
        }
        $ano = substr($data, 5, 4);
        
        return "$dia de $mes de $ano";
    } else {
        return "Não Definido";
    }
}
?>

<div class="notebook" id="details-dialog">
    <div class="tabs-header">
        <div class="tab-header selected">
            Sobre o Curso
        </div>
        <div class="tab-header">
            Sobre a Instituição
        </div>
        <button class="btn-close"><i class="fa fa-times"></i> Fechar</button>
    </div>
    <div class="tabs">
        <div class="tab">
            <div class="label">Nome</div>
            <div class="value"><?= utf8_encode($data['nome_do_curso']) ?></div>
            <div class="label">Início do funcionamento</div>
            <div class="value"><?= parser_data($data['inicio_do_funcionamento']) ?></div>
            <div class="label">Modalidade</div>
            <div class="value"><?= utf8_encode($data['modalidade']) ?></div>
            <div class="label">Nível</div>
            <div class="value"><?= utf8_encode($data['nivel']) ?></div>
            <div class="label">Grau Acadêmico</div>
            <div class="value"><?= utf8_encode($data['grau_academico']) ?></div>
            <div class="label">Periodo</div>
            <ul>
                <li>            
                    <input type="checkbox" <?= $data['eh_matutino'] === '1' ? "checked" : "" ?> onclick="return false;"/>
                    <label>Matutino</label>
                </li>
                <li>            
                    <input type="checkbox" <?= $data['eh_vespertino'] === '1' ? "checked" : "" ?> onclick="return false;"/>
                    <label>Vespertino</label>
                </li>
                <li>            
                    <input type="checkbox" <?= $data['eh_noturno'] === '1' ? "checked" : "" ?> onclick="return false;"/>
                    <label>Noturno</label>
                </li>
                <li>            
                    <input type="checkbox" <?= $data['eh_integral'] === '1' ? "checked" : "" ?> onclick="return false;"/>
                    <label>Integral</label>
                </li>
                <li>            
                    <input type="checkbox" <?= $periodo_definido ? "" : "checked" ?> onclick="return false;"/>
                    <label>Não se aplica</label>
                </li>
            </ul>
            <div class="label">Programa</div>
            <div class="value"><?= utf8_encode($data['codigo_do_programa']) ?> - <?= utf8_encode($data['nome_do_programa']) ?></div>
            <div class="label">Área Detalhada</div>
            <div class="value"><?= utf8_encode($data['area_detalhada']) ?></div>
            <div class="label">Área Específica</div>
            <div class="value"><?= utf8_encode($data['area_especifica']) ?></div>
            <div class="label">Área Geral</div>
            <div class="value"><?= utf8_encode($data['area_geral']) ?></div>
        </div>
        <div class="tab">
            <div class="label">Nome da Instituição</div>
            <div class="value"><?= utf8_encode($data['nome_da_instituicao']) ?> (<?= utf8_encode($data['sigla_da_instituicao']) ?>)</div>
            <div class="label">Tipo da Organização</div>
            <div class="value"><?= utf8_encode($data['tipo_da_organizacao']) ?></div>
            <div class="label">Rede</div>
            <div class="value"><?= utf8_encode($data['rede']) ?></div>
            <div class="label">Natureza Pública</div>
            <div class="value"><?= utf8_encode($data['natureza_publica']) ?></div>
            <div class="label">Natureza Privada</div>
            <div class="value"><?= utf8_encode($data['natureza_privada']) ?></div>
            <div class="label">Múnicipio</div>
            <div class="value"><?= utf8_encode($data['codigo_municipio']) ?> - <?= utf8_encode($data['nome_do_municipio']) ?></div>
            <div class="label">Geolocalização do Múnicipio</div>
            <div class="value">latitude: <i><?= $data['latitude_municipio'] ?></i> e longitude: <i><?= $data['longitude_municipio'] ?></i></div>
            <div class="label">Estado</div>
            <div class="value"><?= utf8_encode($data['nome_do_estado']) ?> (<?= $data['sigla_do_estado'] ?>)</div>
            <div class="label">Região</div>
            <div class="value"><?= utf8_encode($data['nome_da_regiao']) ?></div>
        </div>
    </div>
</div>