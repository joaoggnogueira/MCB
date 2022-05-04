<?PHP
if (!isset($data)) {
    echo "Não é permitido acesso direto! 0x0001";
    exit();
}
$enade_enable = constant('ENABLE_ENADE');

if ($data['mapa'] == "2") {
    $enade_enable = false;
}

include_once './htmlappend/generator.php';
$periodo_definido = $data['eh_matutino'] === '1' || $data['eh_vespertino'] === '1' || $data['eh_noturno'] === '1' || $data['eh_integral'] === '1';

function get_hash_date() {
    return array(
        "JAN" => "JANEIRO",
        "FEB" => "FEVEREIRO",
        "MAR" => "MARÇO",
        "APR" => "ABRIL",
        "MAY" => "MAIO",
        "JUN" => "JUNHO",
        "JUL" => "JULHO",
        "AUG" => "AGOSTO",
        "SEP" => "SETEMBRO",
        "OCT" => "OUTUBRO",
        "NOV" => "NOVEMBRO",
        "DEC" => "DEZEMBRO"
    );
}

function parser_data($data) {
    if (count($data) !== 0) {
        $dia = substr($data, 0, 2);
        $mes_brute = substr($data, 2, 3);
        $hash = get_hash_date();
        $mes = (isset($hash[$mes_brute]) ? $hash[$mes_brute] : "???");
        $ano = substr($data, 5, 4);
        return "$dia de $mes de $ano";
    } else {
        return "Não Definido";
    }
}

$conceito_enade_campus = $data['conceito_enade_campus'];
?>
<a href="./dashboard/sugestao.php?id_curso=<?= $data['id_curso'] ?>" <?= ($data['mapa'] == "2" ? "" : "style='display:none'") ?>>
    <button class="transparent-button">Sugerir Edição</button>
</a>
<div class="notebook" id="details-dialog">
    <div class="tabs-header">
        <div class="tab-header selected">
            Curso
        </div>
        <div class="tab-header">
            Instituição
        </div>
        <div class="tab-header" <?= ($enade_enable ? '' : 'style="display: none"') ?>>
            Conceito Enade
        </div>
        <div class="tab-header">
            Adicional
        </div>
        <button class="btn-close"><i class="fa fa-times"></i> Fechar</button>
    </div>
    <div class="tabs">
        <div class="tab">
            <div class="label">Nome</div>
            <div class="value"><?= $data['nome_do_curso'] ?></div>
            <div class="label">Início do funcionamento</div>
            <div class="value"><?= parser_data($data['inicio_do_funcionamento']) ?></div>
            <div class="label">Modalidade</div>
            <div class="value"><?= ($data['modalidade']) ?></div>
            <div class="label">Nível</div>
            <div class="value"><?= ($data['nivel']) ?></div>
            <?PHP if ($data['grau_academico'] != "N/D"): ?>
                <div class="label">Grau Acadêmico</div>
                <div class="value"><?= ($data['grau_academico']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['total_de_alunos'] != "N/D"): ?>
                <div class="label">Total de Alunos Vinculados</div>
                <div class="value"><?= ($data['total_de_alunos']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['carga_horaria'] != "N/D"): ?>
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
            <?PHP if ($data['codigo_do_programa'] != "N/D"): ?>
                <div class="label">Programa</div>
                <div class="value"><?= ($data['codigo_do_programa']) ?> - <?= ($data['nome_do_programa']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['area_detalhada'] != "N/D"): ?>
                <div class="label">Área Detalhada</div>
                <div class="value"><?= ($data['area_detalhada']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['area_especifica'] != "N/D"): ?>
                <div class="label">Área Específica</div>
                <div class="value"><?= ($data['area_especifica']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['area_geral'] != "N/D"): ?>
                <div class="label">Área Geral</div>
                <div class="value"><?= ($data['area_geral']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['nota'] != "0"): ?>
                <div class="label">Avaliação <?= $data['avaliacao'] ?></div>
                <div class="value">Nota <?= $data['nota'] ?></div>
            <?PHP endif; ?>
        </div>
        <div class="tab">
            <div class="label">Instituição</div>
            <div class="value"><?= $data['id_instituicao'] ?> - <?= ($data['nome_da_instituicao']) ?> (<?= utf8_encode($data['sigla_da_instituicao']) ?>)</div>
            <?PHP if ($data['tipo_da_organizacao'] != "N/D"): ?>
                <div class="label">Tipo da Organização</div>
                <div class="value"><?= ($data['tipo_da_organizacao']) ?></div>
            <?PHP endif; ?>
            <?PHP if (($data['local_de_oferta']) != "NÃO SE APLICA/INDEFINIDO"): ?>
                <div class="label">Campus / Local de Oferta do Curso</div>
                <div class="value"><?= ($data['local_de_oferta']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['mantenedora'] != "N/D"): ?>
                <div class="label">Mantenedora - CNPJ</div>
                <div class="value"><?= ($data['mantenedora']) ?></div>
            <?PHP endif; ?>
            <?PHP if ($data['rede'] != "N/D"): ?>
                <div class="label">Rede</div>
                <div class="value"><?= ($data['rede']) ?></div>
            <?PHP endif; ?>
            <div class="label">Natureza</div>
            <div class="value"><?= ($data['natureza_publica']) ?></div>
            <?PHP if ($data['natureza_privada'] != "N/D"): ?>
                <div class="label">Natureza Jurídica</div>
                <div class="value"><?= ($data['natureza_privada']) ?></div>
            <?PHP endif; ?>
            <div class="label">Múnicipio</div>
            <div class="value"><?= ($data['codigo_municipio']) ?> - <?= ($data['nome_do_municipio']) ?> (<?= $data['sigla_do_estado'] ?>)</div>
        </div>
        <div class="tab">
            <div class="controlgroup_vertical">
                <div class="label">Nome da Instituição</div>
                <div class="value"><?= ($data['nome_da_instituicao']) ?> (<?= ($data['sigla_da_instituicao']) ?>)</div>
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
                    <div class="clabel label-33 f12 tabulation"><b>Bruta:</b> <span name="nota_bruta_fg">...</span></div>
                    <div class="clabel label-33 f12"><b>Padronizada:</b> <span name="nota_padronizada_fg">...</span></div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f15 bold">Componente Específico (75%)</div>
                <div class="clabel label-100">
                    <div class="clabel label-33 f12 tabulation"><b>Bruta:</b> <span name="nota_bruta_ce">...</span></div>
                    <div class="clabel label-33 f12"><b>Padronizada:</b> <span name="nota_padronizada_ce">...</span></div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f15 bold">Nota GERAL</div>
                <div class="clabel label-100">
                    <div class="clabel label-33 f12 tabulation"><b>Bruta:</b>  <span name="nota_bruta_geral">...</span></div>
                    <div class="clabel label-33 f12"><b>Padronizada:</b> <span name="nota_padronizada_geral">...</span></div>
                </div>
                <div class="clabel horizontal-space"></div>
                <div class="clabel label-100 f12"><span name="n_inscritos">...</span> inscritos e <span name="n_participantes">...</span> participantes</div>
            </div>
        </div>
        <div class="tab">
            <div class="controlgroup_vertical">
                <div class="clabel label-100">
                    <?PHP if (isset($data['link']) && $data['link'] && $data['link'] !== ""): ?>
                        <div class="label">Link para informações adicionais</div>
                        <a href="<?= $data['link'] ?>"><?= $data['link'] ?></a>
                    <?PHP endif; ?>
                </div>
                <div id="rich_editor">

                </div>
            </div>
        </div>
    </div>
</div>