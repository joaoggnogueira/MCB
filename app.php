<?PHP
include "config/config.php";

include_once "./controllers/DatabaseController.php";
include './models/RelatorioModel.php';

$model = new RelatorioModel();
$mapaId = false;
if (isset($_GET['mapa'])) {
    $mapaId = (int) $_GET['mapa'];
} else {
    $mapaId = 1;
}

$mapaInfo = $model->getMapaInfo($mapaId);

$listGrau = $model->listGrau($mapaId);
$listRede = $model->listRede($mapaId);
$listModalidade = $model->listModalidade($mapaId);
$listNatureza = $model->listNatureza($mapaId);
$listNivel = $model->listNivel($mapaId);
$listPrograma = $model->listPrograma($mapaId);
$listTipoOrganizacao = $model->listTipoOrganizacao($mapaId);

$listNaturezaDepartamento = $model->listNaturezaDepartamento();

$listEstado = $model->listEstado();
$listRegiao = $model->listRegiao();

if ($mapaId == 1) {
    $avaliacao = array(
        array("id" => 0, "nome" => "INDEFINIDO"),
        array("id" => 1, "nome" => utf8_decode("1 (0.0 até 1.0)")),
        array("id" => 2, "nome" => utf8_decode("2 (1.0 até 2.0)")),
        array("id" => 3, "nome" => utf8_decode("3 (2.0 até 3.0)")),
        array("id" => 4, "nome" => utf8_decode("4 (3.0 até 4.0)")),
        array("id" => 5, "nome" => utf8_decode("5 (4.0 até 5.0)"))
    );
} else {
    $avaliacao = array(
        array("id" => 1, "nome" => "1"),
        array("id" => 2, "nome" => "2"),
        array("id" => 3, "nome" => "3"),
        array("id" => 4, "nome" => "4"),
        array("id" => 5, "nome" => "5"),
        array("id" => 6, "nome" => "6"),
        array("id" => 7, "nome" => "7"),
        array("id" => 7, "nome" => "8"),
        array("id" => 7, "nome" => "9"),
        array("id" => 'A', "nome" => "A")
    );
}

$enade_enable = constant('ENABLE_ENADE');

function encode_all_strings($arr) {
    return utf8_encode($arr);
}

if (is_array($mapaInfo)) {
    $mapaInfo = array_map("encode_all_strings", $mapaInfo);
}
?>
<html lang="pt-BR">
    <head>
        <title>Mapa da Computação</title>
        <meta name="viewport" content="width=device-width">
        <meta charset="UTF-8" />
        <meta name="theme-color" content="#4267b2"/>
        <meta name="Description" content="Mapa dos Cursos de Computação e Similares no Território Nacional">
        <meta name="author" content="João G. G. Nogueira">
        <meta name="keywords" content="Mapa, Ciência da Computação, Tecnologia da Informação, Visualização de Dados">

        <link rel="manifest" href="manifest.json"/>
        <meta name="robots" content="all">
        <meta name="googlebot-news" content="noindex" />
        <link rel="shortcut icon" href="images/icon/mcb-32.png"/>

        <script>
            ROOT_APP = "<?= constant('ROOT_APP'); ?>";
            window.onload_all = function () {
                cUI.mapCtrl.setMapInfo(<?= json_encode($mapaInfo) ?>);
                cUI.mapCtrl.requestUpdate(cUI.filterCtrl.getFilters());
<?PHP if (!$enade_enable): ?>
                    $("#filterbar-tab-task").hide();
                    $("#graph-item-enade").hide();
<?PHP endif; ?>
            }
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <link async rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/ju/dt-1.10.18/datatables.min.css"/>
        <script async type="text/javascript" src="https://cdn.datatables.net/v/ju/dt-1.10.18/datatables.min.js"></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.24.1/sweetalert2.min.js'></script>
        <script src='<?= resource_script("cData.js"); ?>'></script>
        <script src='<?= resource_script("cMapControl.js"); ?>'></script>
        <script src='<?= resource_script("cRequest.js"); ?>'></script>
        <script src='<?= resource_script("cFilterControl.js"); ?>'></script>
        <script src='<?= resource_script("cMarkerDialogControl.js"); ?>'></script>
        <script src='<?= resource_script("cInterfaceController.js"); ?>'></script>
        <script src='<?= resource_script("cNotebookControl.js"); ?>'></script>
        <script src='<?= resource_script("app.js"); ?>'></script>
        <script src='<?= resource_script("polyfill.js"); ?>'></script>

        <script src='<?= resource_script("cUserConfig.js"); ?>'></script>

        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6A2l8RrNfmBdbVI-kMjRHBoZmBa1e4IU&libraries=places&callback=initMap"></script>

        <link async rel="stylesheet" href="<?= resource_css("font.css"); ?>">
        <link async href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.24.1/sweetalert2.min.css"/>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css"/>
        <script src="https://unpkg.com/promise-polyfill"></script>

        <link rel="stylesheet" href="<?= resource_css("app.css"); ?>"/>
        <link async href="<?= resource_css("responsive.css"); ?>" rel="stylesheet" media="(max-width: 510px)"/>
        <link async rel="stylesheet" href='<?= resource_css("thirdparty/tipsy.css"); ?>'/>

        <script async src="https://d3js.org/d3.v3.min.js"></script>
        <script async src='<?= resource_script("thirdparty/Donut3D.js"); ?>'></script>
        <link async src='<?= resource_css("thirdparty/Donut3D.css"); ?>'/>
        <script src='<?= resource_script("thirdparty/jquery.tipsy.js"); ?>'></script>
        <script async src='<?= resource_script("cGraphs.js"); ?>'></script>
    </head>
    <body id='body' class='day-theme'>
        <div class="header-group" id="header-group">
            <a class="button-home" href="#"><i class="fa fa-home"></i></a>
            <div class="text"><?= $mapaInfo['title'] ?><br/><span style="font-size: 12px">Fonte: <?= $mapaInfo['fonte'] ?> <?= $mapaInfo['ano'] ?></span></div>
        </div>
        <div id="input-group-search" class="input-group">
            <label for="pac-input" style="position: absolute"></label>
            <select autocomplete="off" name="select-search" id="select-search">
                <option selected value="municipio">Pesquisar por Localização</option>
                <option value="instituicao">Pesquisar por Instituição</option>
            </select>
            <input autocomplete="off" id="pac-input" class="pac-input" type="text" placeholder="Nome do Local, Município e Organização">
            <input autocomplete="off" id="inst-input" class="pac-input" type="text" placeholder="Sigla ou Nome da Instituição">
            <a class="pac-button" id="close-filter-inst-btn">
                <i class='pac-addon-icon fa fa-times'></i>
                <div class="pac-addon-header"></div>
                <div class="pac-addon-content"></div>
            </a>
        </div>
        <div class="tree-process-selector" id="selected-mode">
            <button id="config-visualizacao" class="tipsy-boot" title="Configurações da visualização atual">
                <i class="fa fa-cog"></i>
            </button>
            <div class="content">
                <ul>
                    <li>
                        <select autocomplete="off" class="text" id="visual-selected-text">
                            <option value="0" selected>Com agrupamento</option>
                            <option value="1">Sem agrupamento</option>
                            <option value="2">Circulo Ponderado</option>
                            <!--<option value="3">Escala de Cores</option>-->
                        </select>
                        <div class="next"></div>
                    </li>
                    <li>
                        <select autocomplete="off" class="text" id="marker-selected-text">
                            <option value="0" selected>Por município</option>
                            <option value="1">Por estado</option>
                            <option value="2">Por região</option>
                        </select>
                    </li>
                </ul>
            </div>
        </div>
        <div class='theater' id="theater-details">
            <div class="modal-content"></div>
        </div>
        <div title="Filtros" class='btn-toggle-filter tipsy-boot' id='btn-toggle-filter' notifyCount="0">
            <i class="fa fa-filter"></i>
        </div>
        <div class="filterbar" id="filterbar">
            <div class="filterbar-wrapper">
                <div class="filterbar-div filterbar-header">
                    <div class='title'>Filtros</div>
                    <div class="counter" id="counter-filters">
                        <div class="total"></div>
                        <div class="text"></div>
                    </div>
                </div>
                <div class="filterbar-tab-headers">
                    <div class="filterbar-tab-header selected" title="Sobre o curso"><i class="fa fa-book"></i></div>
                    <div class="filterbar-tab-header" title="Sobre a instituição"><i class="fa fa-university"></i></div>
                    <div class="filterbar-tab-header" title="Sobre o localização"><i class="fa fa-map"></i></div>
                    <div class="filterbar-tab-header" id="filterbar-tab-task" title="Sobre as avaliações"><i class="fa fa-tasks"></i></div>
                </div>
                <div class="filterbar-div filterbar-content" id="filter-list">
                    <ul class='filter-list'>
                        <?PHP
                        resource_component("Filter.php", array("li-name" => "grau", "id" => "grau", "title" => "Grau Académico", "lista" => $listGrau));
                        resource_component("Filter.php", array("li-name" => "modalidades", "id" => "modalidade", "title" => "Modalidade", "lista" => $listModalidade));
                        resource_component("Filter.php", array("li-name" => "nivel", "id" => "nivel", "title" => "Nível", "lista" => $listNivel));
                        resource_component("Filter.php", array("li-name" => "programa", "id" => "programa", "title" => "Programa", "lista" => $listPrograma));
                        ?>
                    </ul>
                    <ul class='filter-list' style="display: none">
                        <?PHP
                        resource_component("Filter.php", array("li-name" => "rede", "id" => "rede", "title" => "Rede", "lista" => $listRede));
                        resource_component("Filter.php", array("li-name" => "natureza", "id" => "natureza", "title" => "Natureza Jurídica", "lista" => $listNatureza));
                        resource_component("Filter.php", array("li-name" => "naturezadep", "id" => "natureza_departamento", "title" => "Natureza", "lista" => $listNaturezaDepartamento));
                        resource_component("Filter.php", array("li-name" => "tipoorganizacao", "id" => "tipoorganizacao", "title" => "Tipo de Organização", "lista" => $listTipoOrganizacao));
                        ?>
                    </ul>
                    <ul class='filter-list' style="display: none">
                        <?PHP
                        resource_component("Filter.php", array("li-name" => "estado", "id" => "estado", "title" => "Estado", "lista" => $listEstado));
                        resource_component("Filter.php", array("li-name" => "regiao", "id" => "regiao", "title" => "Região", "lista" => $listRegiao));
                        ?>
                    </ul>
                    <ul class='filter-list' style="display: none">
                        <?PHP
                        resource_component("Filter.php", array("li-name" => "enade", "id" => "enade", "title" => "Nota ".$mapaInfo['avaliacao'], "lista" => $avaliacao));
                        ?>
                    </ul>
                </div>
                <div class="filterbar-div filterbar-footer">
                </div>
            </div>
        </div>
        <div id="map">
        </div> 
        <div id="marker-dialog" class="marker-dialog">
            <div class="header">
                <button class="close-btn"><i class="fa fa-chevron-up"></i> FECHAR</button>
            </div>
            <div class="description">Mostrando resultados para o Município</div>
            <div class="overview" id="name-mun">
                %Município%(%UF%)
            </div>
            <div class="description alert"><i class="fa fa-exclamation-triangle"></i> Os resultados estão sobre influência dos filtros selecionados</div>
            <div class="notebook" id="notebook-marker-dialog">
                <div class="tabs-header">
                    <div class="tab-header selected">
                        Cursos
                    </div>
                    <div class="tab-header">
                        Gráficos
                    </div>
                    <div class="tab-header" id="tabheadermun">
                        Sobre o %LOCAL%
                    </div>
                    <div class="tab-header" style="display: none">
                        Exportar
                    </div>
                </div>
                <div class="tabs">
                    <div class="tab" id="cursos-tab">
                        <table class="table-list" id="table-cursos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome (<?= $mapaInfo['fonte'] ?>)</th>
                                    <th>Instituição</th>
                                </tr>
                            </thead>
                        </table>
                        <?PHP if($mapaId == 1): ?>
                        <div class="clabel tabulation f12"> (*) Cursos ofertados na modalidade a distância (EAD)</div>
                        <?PHP endif; ?>
                    </div>
                    <div class="tab" id="graphs-tab">
                        <ul>
                            <?PHP
                            resource_component("Graph.php", array("id" => "grau", "title" => "Grau Académico", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "rede", "title" => "Rede", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "modalidade", "title" => "Modalidade", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "enade", "title" => "Nota ".$mapaInfo['avaliacao'], "type" => "bars"));
                            resource_component("Graph.php", array("id" => "natureza", "title" => "Natureza Privada", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "naturezadep", "title" => "Natureza Pública", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "nivel", "title" => "Nível", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "programa", "title" => "Programa do Curso", "type" => "bars"));
                            resource_component("Graph.php", array("id" => "tipoorganizacao", "title" => "Tipo da Organização", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "estado", "title" => "Estado", "type" => "sector"));
                            ?>
                        </ul>
                    </div>
                    <div class="tab" id="local-tab">
                    </div>
                    <div class="tab" id="export-tab" style="display: none">
                    </div>
                </div>
            </div>
        </div>
        <a class="logotipo" id="logotipo_unesp"><img title="Logotipo da UNESP" width="100" height="36" src="images/logotipos/unesp-placeholder-mini.png" /></a>
        <a class="logotipo" id="logotipo_sbc"><img title="Logotipo do Sociedade Brasileira de Computação" width="30" height="36" src="images/logotipos/sbc_placeholder_mini_2.png"/></a>
        <div id="splash">
            <div class="title">Carregando</div>
            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
        </div>
        <!--        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-122930708-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'UA-122930708-1');
        </script>-->
    </body>
</html>