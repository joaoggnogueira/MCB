<?PHP
if (!isset($data)) {
    echo "Não é permitido acesso direto! 0x0001";
    exit();
}

include_once './htmlappend/generator.php';
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

$conceito_enade_campus = $data['conceito_enade_campus'];
?>

<div class="notebook" id="details-dialog">
    <div class="tabs-header">
        <div class="tab-header selected">
            Curso
        </div>
        <div class="tab-header">
            Instituição
        </div>
        <div class="tab-header">
            Conceito Enade
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
            <div class="label">Código da Instituição</div>
            <div class="value"><?= $data['id_instituicao'] ?></div>
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
        <div class="tab">
            <div class="controlgroup_vertical">
                <div class="label">Nome da Instituição</div>
                <div class="value"><?= utf8_encode($data['nome_da_instituicao']) ?> (<?= utf8_encode($data['sigla_da_instituicao']) ?>)</div>
                <?PHP if (!is_array($conceito_enade_campus)): ?>
                    <div class="label"><i>Falha ao Consultar o Banco</i></div>
                <?PHP else: ?>
                    <?PHP if (empty($conceito_enade_campus) === 0): ?>
                        <div class="label"><i>Nenhuma avaliação disponível para esta instituição</i></div>
                    <?PHP else: ?>
                        <label for="ano_enade" class="w-200 ui-controlgroup-label">Campus</label>
                        <?PHP select("campus_enade", $conceito_enade_campus, "cod_municipio", "nome_municipio", ""); ?>
                        <hr/>
                        <label disabled for="ano_enade" class="w-200 ui-controlgroup-label">Ano do Enade</label>
                        <?PHP select("ano_enade", false, false, false, ""); ?>
                        <hr/>
                        <label disabled for="area_enade" class="w-200 ui-controlgroup-label">Área de Enquadramento</label>
                        <?PHP select("area_enade", false, false, false, ""); ?>
                        <hr/>
                    <?PHP endif; ?>
                <?PHP endif; ?>
            </div>
            <div id="enade-details" style="display: none">
                <div class="clabel label-100">
                    <div class="clabel label-33 f16 bold">Conceito ENADE: </div>
                    <div class="clabel label-33 f16"><h1><span name="conceito_enade_faixa">2</span></h1> (<span name="conceito_enade_continuo">1.69</span>)</div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f15 bold">Formação Geral (25%)</div>
                <div class="clabel label-100">
                    <div class="clabel label-33 f12 tabulation"><b>Bruta:</b> <span name="nota_bruta_fg">50.5</span></div>
                    <div class="clabel label-33 f12"><b>Padronizada:</b> <span name="nota_padronizada_fg">1.5</span></div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f15 bold">Componente Específico (75%)</div>
                <div class="clabel label-100">
                    <div class="clabel label-33 f12 tabulation"><b>Bruta:</b> <span name="nota_bruta_ce">50.5</span></div>
                    <div class="clabel label-33 f12"><b>Padronizada:</b> <span name="nota_padronizada_ce">1.5</span></div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f15 bold">Nota GERAL</div>
                <div class="clabel label-100">
                    <div class="clabel label-33 f12 tabulation"><b>Bruta:</b>  <span name="nota_bruta_geral">50.5</span></div>
                    <div class="clabel label-33 f12"><b>Padronizada:</b> <span name="nota_padronizada_geral">1.5</span></div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f12"><span name="n_inscritos">42</span> inscritos e <span name="n_participantes">40</span> participantes</div>
            </div>
        </div>
    </div>
</div>