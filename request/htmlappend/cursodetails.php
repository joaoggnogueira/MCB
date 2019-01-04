<?PHP
if (!isset($data)) {
    echo "Não é permitido acesso direto! 0x0001";
    exit();
}
$enade_enable = constant('ENABLE_ENADE');

if($data['mapa']=="2"){
    $enade_enable = false;
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
        <div class="tab-header" <?= ($enade_enable?'':'style="display: none"') ?>>
            Conceito Enade
        </div>
        <div class="tab-header" style="display: none">
            Metadados
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
            <?PHP if($data['grau_academico'] != "N/D"): ?>
                <div class="label">Grau Acadêmico</div>
                <div class="value"><?= utf8_encode($data['grau_academico']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['total_de_alunos'] != "N/D"): ?>
                <div class="label">Total de Alunos Vinculados</div>
                <div class="value"><?= utf8_encode($data['total_de_alunos']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['carga_horaria'] != "N/D"): ?>
                <div class="label">Carga Horária</div>
                <div class="value"><?= $data['carga_horaria'] ?></div>
            <?PHP endif; ?>
<!--        <div class="label">Periodo</div>
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
            </ul>-->
            <?PHP if($data['codigo_do_programa'] != "N/D"): ?>
                <div class="label">Programa</div>
                <div class="value"><?= utf8_encode($data['codigo_do_programa']) ?> - <?= utf8_encode($data['nome_do_programa']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['area_detalhada'] != "N/D"): ?>
                <div class="label">Área Detalhada</div>
                <div class="value"><?= utf8_encode($data['area_detalhada']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['area_especifica'] != "N/D"): ?>
                <div class="label">Área Específica</div>
                <div class="value"><?= utf8_encode($data['area_especifica']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['area_geral'] != "N/D"): ?>
                <div class="label">Área Geral</div>
                <div class="value"><?= utf8_encode($data['area_geral']) ?></div>
            <?PHP endif; ?>
        </div>
        <div class="tab">
            <div class="label">Instituição</div>
            <div class="value"><?= $data['id_instituicao'] ?> - <?= utf8_encode($data['nome_da_instituicao']) ?> (<?= utf8_encode($data['sigla_da_instituicao']) ?>)</div>
            <?PHP if($data['tipo_da_organizacao'] != "N/D"): ?>
                <div class="label">Tipo da Organização</div>
                <div class="value"><?= utf8_encode($data['tipo_da_organizacao']) ?></div>
            <?PHP endif; ?>
            <?PHP if(utf8_encode($data['local_de_oferta']) != "NÃO SE APLICA/INDEFINIDO"): ?>
                <div class="label">Campus / Local de Oferta do Curso</div>
                <div class="value"><?= utf8_encode($data['local_de_oferta']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['mantenedora'] != "N/D"): ?>
                <div class="label">Mantenedora - CNPJ</div>
                <div class="value"><?= utf8_encode($data['mantenedora']) ?></div>
            <?PHP endif; ?>
            <?PHP if($data['rede'] != "N/D"): ?>
                <div class="label">Rede</div>
                <div class="value"><?= utf8_encode($data['rede']) ?></div>
            <?PHP endif; ?>
            <div class="label">Natureza</div>
            <div class="value"><?= utf8_encode($data['natureza_publica']) ?></div>
            <?PHP if($data['natureza_privada'] != "N/D"): ?>
                <div class="label">Natureza Jurídica</div>
                <div class="value"><?= utf8_encode($data['natureza_privada']) ?></div>
            <?PHP endif; ?>
            <div class="label">Múnicipio</div>
            <div class="value"><?= utf8_encode($data['codigo_municipio']) ?> - <?= utf8_encode($data['nome_do_municipio']) ?> (<?= $data['sigla_do_estado'] ?>)</div>
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
        <div class="tab">
            <?= utf8_encode(print_r($data,true)) ?>
        </div>
    </div>
</div>