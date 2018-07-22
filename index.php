<?PHP
include "config/config.php";

include_once "./controllers/DatabaseController.php";
include './models/RelatorioModel.php';

$model = new RelatorioModel();

$listGrau = $model->listGrau();
$listRede = $model->listRede();
$listModalidade = $model->listModalidade();
$listNatureza = $model->listNatureza();
$listNaturezaDepartamento = $model->listNaturezaDepartamento();
$listNivel = $model->listNivel();
$listPrograma = $model->listPrograma();
$listEstado = $model->listEstado();
$listRegiao = $model->listRegiao();
$listTipoOrganizacao = $model->listTipoOrganizacao();

$savedconfig = false;
if(isset($_GET['savedconfig'])){
    $savedconfig = (int)$_GET['savedconfig'];
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
        <script>
            ROOT_APP = "<?= constant('ROOT_APP'); ?>";
            window.onload_all = function () { 
                <?PHP if($savedconfig): ?>
                    cUI.mapCtrl.loadData(<?= $savedconfig ?>);
                <?PHP else: ?>
                    cUI.mapCtrl.requestUpdate(cUI.filterCtrl.getFilters());
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
        <script src='<?= resource_script("cSidebarControl.js"); ?>'></script>
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

        <link rel="stylesheet" href="<?= resource_css("principal.css"); ?>"/>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.5.0/d3.min.js"></script>
        <script async src='<?= resource_script("thirdparty/Donut3D.js"); ?>'></script>
        <link async src='<?= resource_css("thirdparty/Donut3D.css"); ?>'/>
    </head>
    <body id='body' class='day-theme'>
        <div id="input-group-search" class="input-group">
            <label for="pac-input" style="position: absolute"></label>
            <select name="select-search" id="select-search">
                <option value="municipio">Pesquisar por Localização</option>
                <option value="instituicao">Pesquisar por Instituição</option>
            </select>
            <input id="pac-input" class="pac-input" type="text" placeholder="Nome do Local, Município e Organização">
            <input id="inst-input" class="pac-input" type="text" placeholder="Sigla ou Nome da Instituição">
            <a class="pac-button" id="close-filter-inst-btn">
                <i class='pac-addon-icon fa fa-times'></i>
                <div class="pac-addon-header"></div>
                <div class="pac-addon-content"></div>
            </a>
        </div>
        <div class="tree-process-selector" id="selected-mode">
            <button id="config-visualizacao">
                <i class="fa fa-cog"></i>
            </button>
            <div class="content">
                <ul>
                    <li>
                        <select class="text" id="visual-selected-text">
                            <option value="0">Marcadores com agrupamento</option>
                            <option value="1">Marcadores sem agrupamento</option>
                            <option value="2">Circulo Ponderado</option>
                        </select>
                        <div class="next"></div>
                    </li>
                    <li>
                        <select class="text" id="marker-selected-text">
                            <option value="0">Por município</option>
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
        <div class='theater' id="theater-search">
            <div class="modal-content"></div>
        </div>
        <div class='theater' id="theater-sidebar">
            <div class="theater-content" id="theater-content">
                <div class="theater-about" id="theater-overview">
                    <h1 class="theater-about-title">
                        Mapa dos Cursos de Computação e Similares no Território Nacional
                    </h1>
                    <h4 class="theater-about-content">
                        Esta ferramenta traz a relação de cursos de computação (ou similares) com os municípios onde foram ou estão sendo realizados dentro o território nacional. A ferramenta traz várias técnicas de visualização de dados para tornar transparente a concentração dos cursos, junto com um controle de diversos filtros. 
                        <br/><br/>
                        O mapa não traz nenhuma informação sobre questões orçamentárias, número de estudantes, número de vagas ofertadas, e dados pessoais sobre os educandos, educadores, coordenadores, diretores, entre outros.
                        <br/><br/>
                        
                    </h4>
                    <hr/>
                    <div class="theater-about-footer">
                        Os dados foram obtidos a partir de um relatório do Censo de 2015, e modelados para de banco de dados.
                        <br/>
                        <details>
                            <summary>Mais informações sobre a fonte dos dados:</summary>
                            <br/>
                            Ministério da Educação - Instituto Nacional de Estudos e Pesquisas Educacionais Anísio Teixeira<br/>	
                            <br/>
                            Educação Superior - Relação dos Cursos de COMPUTAÇÃO - GRADUAÇÃO E SEQUENCIAIS - PRESENCIAIS E A DISTÂNCIA, segundo as Instituições de Ensino e o Curso<br/>			
                        </details>
                        <hr/>
                        <h4>2018</h4>
                        Desenvolvedor:
                        <a target="_blank" rel="noopener" href="https://www.linkedin.com/in/joão-gabriel-gomes-nogueira-96133040/">João Gabriel Gomes Nogueira</a>
                        <br/>
                        Coordenador:
                        <a target="_blank" rel="noopener" href="http://www.unesp.br/PortalPROPe/unesp/fct/dmec/ronaldo-celso-messias-correia/informacoes/">Ronaldo Celso Messias Correia</a>
                    </div>
                    <hr/>
                    <img sync style="padding: 5px" src="./images/logotipos/sbc.png" width="69" height="100"/>
                    <img sync style="padding: 5px" src="./images/logotipos/unesp-white.svg" width="300" height="100"/>
                </div>
                <div class="theater-about" id="theater-visualizacao">
                    <div class="theater-about-title">
                        <i class="fa fa-map"></i>&nbsp;&nbsp;&nbsp;Visualização
                    </div>
                    <div class="theater-about-content max-content">
                        Escolha o tipo de exibição da visualização:
                        <br/><br/>
                        <table>
                            <tr>
                                <td>
                                    <img width="150px" alt="exemplo de marcador com agrupamento" src='./images/ui/marcador/1.jpeg'/>
                                </td>
                                <td style="vertical-align: top"> 
                                    <div class="label-title">Com agrupamento</div>
                                    <div class="label-content">Marcadores muito próximos serão agrupados</div>
                                    <button class="button-theater button-toggle-theater" disabled title="Selecionar"></button><button class="button-theater button-addon-theater"><i class="fa fa-cog"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img width="150px" alt="exemplo de marcador sem agrupamento" src='./images/ui/marcador/2.jpeg'/>
                                </td>
                                <td style="vertical-align: top"> 
                                    <div class="label-title">Sem agrupamento</div>
                                    <div class="label-content">Pode levar muito tempo para processar devido a quantidade de marcadores</div>
                                    <button class="button-theater button-toggle-theater" title="Selecionar"></button><button class="button-theater button-addon-theater"><i class="fa fa-cog"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img width="150px" alt="exemplo de marcador ponderado" src='./images/ui/marcador/3.jpeg'/>
                                </td>
                                <td> 
                                    <div class="label-title">Circulo Ponderado</div>
                                    <div class="label-content">Não possui interação, mas permite ver a concentração de caracterizações</div>
                                    <button class="button-theater button-toggle-theater" title="Selecionar"></button><button class="button-theater button-addon-theater"><i class="fa fa-cog"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="theater-about" id="theater-markers">
                    <div class="theater-about-title">
                        <i class="fa fa-map-marker"></i>&nbsp;&nbsp;&nbsp;Modo de Marcador
                    </div>
                    <div class="theater-about-content max-content">
                        Escolha o modo de exibição dos marcadores:
                        <br/><br/>
                        <table>
                            <tr>
                                <td>
                                    <img width="150px" alt="exemplo de modo por múnicipio" src='./images/ui/marcador/6.jpeg'/>
                                </td>
                                <td style="vertical-align: top"> 
                                    <div class="label-title">Por Município</div>
                                    <div class="label-content">Cada marcador representa um múnicipio</div>
                                    <button class="button-theater button-toggle-theater" disabled title="Selecionar"></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img width="150px" alt="exemplo de modo por estado" src='./images/ui/marcador/5.jpeg'/>
                                </td>
                                <td style="vertical-align: top"> 
                                    <div class="label-title">Por Estado</div>
                                    <div class="label-content">Cada marcador representa um estado</div>
                                    <button class="button-theater button-toggle-theater" title="Selecionar"></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img width="150px" alt="exemplo de modo por região" src='./images/ui/marcador/4.jpeg'/>
                                </td>
                                <td> 
                                    <div class="label-title">Por Região</div>
                                    <div class="label-content">Cada marcador representa uma região</div>
                                    <button class="button-theater button-toggle-theater" title="Selecionar"></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="theater-about" id="theater-help">
                    <div class="theater-about-title">
                        <i class="fa fa-question-circle"></i>&nbsp;&nbsp;&nbsp;Ajuda
                    </div>
                    <div class="theater-about-content">
                        <table>
                            <tr>
                                <td>
                                    <img async width="120px" src='./images/ui/ajuda/empty.jpeg' alt="exemplo de mapa vazio"/>
                                </td>
                                <td>
                                    <div class="label-title">Por que não consigo ver nenhum marcador?</div>
                                    <div class="label-content">
                                        Revise os filtros selecionados, provalvemente o uso vários filtros entraram em
                                        contradição fazendo que a ferramenta não encontre um curso dentro de uma condição impossível. 
                                        <br/>Por exemplo: Filtrar cursos da rede pública com fins lucrativos, ou tentar visualizar cursos que são somente do Sul e 
                                        Acre ao mesmo tempo.
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <hr/>
                        <div class="label-title">O que significa o número dentro do marcador?</div>
                        <div class="label-content">
                            Depende do marcador que você está se referindo:
                            <table>
                                <tr>
                                    <td><img async width="50px" src='./images/ui/ajuda/1.jpeg' alt="exemplo de marcador simples"/></td>
                                    <td>O número dentro do marcador simples, sempre informa o número de cursos naquele municipio, de acordo com os filtros selecionados.</td>
                                </tr>
                                <tr>
                                    <td><img async width="50px" src='./images/ui/ajuda/2.jpeg' alt="exemplo de marcador de agrupamento"/></td> 
                                    <td> No caso do marcador de agrupamento ele informa o número de múnicipios agrupados neles, e não o número de cursos naquela região, de acordo com os filtros selecionados.</td>
                                </tr>
                                <tr>
                                    <td><img async width="50px" src='./images/ui/ajuda/3.jpeg' alt="exemplo de marcador ponderado"/></td>
                                    <td>O marcador em círculo ponderado, o raio informa a quantidade, ou concetração de cursos em cada múnicpio, de acordo com os filtros selecionados.</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="theater-about" id="theater-save">
                    <div class="theater-about-title">
                        <i class="fa fa-save"></i>&nbsp;&nbsp;&nbsp;Salvar configurações da visualização
                    </div>
                    <div class="theater-about-content">
                        <div>
                            Você pode salvar a configurações dos filtros e o tipo de marcador selecionado, para ser recuperado mais tarde.
                            <br/>As configurações salvas serão visiveis para outros usuários.
                        </div>
                        <hr/>
                        <div class="label-title">Filtros Ativados</div>
                        <div class="label-content" id="filters-to-save">
                            
                        </div>
                        <div class="label-title">Tipo de Marcador</div>
                        <div class="label-content" id="marker-to-save">
                            
                        </div>
                        <div class="label-title">Rótulo</div>
                        <div class="label-content">Insira o rótulo para identificar a configurações a ser salva</div>
                        <input type="text" id="rotulo" maxlength="50"/>
                        <br/>
                        <button id='ok-save'>Salvar</button>
                    </div>
                </div>
                <div class="theater-about" id="theater-open">
                    <div class="theater-about-title">
                        <i class="fa fa-folder-open"></i>&nbsp;&nbsp;&nbsp;Abrir configurações da visualização
                    </div>
                    <div class="theater-about-content" id="theater-table-load">
                        <table id="table-load" class="display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Rótulo</th>
                                    <th>Tipo de Marcador</th>
                                    <th>Data de revisão</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-toggle-sidebar" id="btn-toggle-sidebar"><i class="fa fa-bars"></i></div>
        <div class="sidebar" id="sidebar">
            <div class='sidebar-content'>
                <div class='sidebar-title'>
                    DASHBOARD
                </div>
                <ul class='list-buttons-sidebar'>
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "filters", "fa-icon" => "fa-filter", "text" => "Filtros")
                        );
                        ?>
                    </li>
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "visualizacao", "fa-icon" => "fa-map", "text" => "Visualização")
                        );
                        ?>
                    </li>     
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "markers", "fa-icon" => "fa-map-marker", "text" => "Modo de Marcador")
                        );
                        ?>
                    </li>     
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "estatistica", "fa-icon" => "fa-file", "text" => "Estatística")
                        );
                        ?>
                    </li>     
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "help", "fa-icon" => "fa-question", "text" => "Ajuda")
                        );
                        ?>
                    </li> 
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "save", "fa-icon" => "fa-save", "text" => "Salvar")
                        );
                        ?>
                    </li> 
                    <li class='item-list-buttons-sidebar'>
                        <?PHP
                        resource_component(
                            "buttonSidebar.php", array("id" => "load", "fa-icon" => "fa-folder-open", "text" => "Carregar")
                        );
                        ?>
                    </li> 
                </ul>
                <div class='sidebar-footer'>
                    <button id='random-theme-btn' class='simple-btn' title="Alterar cor tema"><i class='fa fa-paint-brush'></i></button>
                </div>
            </div>
        </div>
        <div class='btn-toggle-filter' id='btn-toggle-filter'>
            <i class="fa fa-filter"></i>
        </div>
        <div class="filterbar" id="filterbar">
            <div class="filterbar-wrapper">
                <div class="filterbar-div filterbar-header">
                    <div class='title'>Filtros</div>
                    <label for="keep-update-filter"><input type="checkbox" id="keep-update-filter"/> Auto-Atualizar</label>
                    <button id="reset-filter"><i class="fa fa-refresh"></i>  Limpar Tudo</button>
                    <div class="counter" id="counter-filters">
                        <div class="total"></div>
                        <div class="text"></div>
                    </div>
                </div>
                <div class="filterbar-div filterbar-content">
                    <ul class='filter-list' id="filter-list">
                        <li name="grau" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "grau", "title" => "Grau", "lista" => $listGrau)
                            );
                            ?>
                        </li>
                        <li name="rede" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "rede", "title" => "Rede", "lista" => $listRede)
                            );
                            ?>
                        </li>
                        <li name="modalidades" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "modalidade", "title" => "Modalidade", "lista" => $listModalidade)
                            );
                            ?>
                        </li>
                        <li name="natureza" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "natureza", "title" => "Natureza Privada", "lista" => $listNatureza)
                            );
                            ?>
                        </li>
                        <li name="naturezadep" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "natureza_departamento", "title" => "Natureza Pública", "lista" => $listNaturezaDepartamento)
                            );
                            ?>
                        </li>
                        <li name="nivel" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "nivel", "title" => "Nível", "lista" => $listNivel)
                            );
                            ?>
                        </li>
                        <li name="programa" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "programa", "title" => "Programa", "lista" => $listPrograma)
                            );
                            ?>
                        </li>
                        <li name="tipoorganizacao" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "tipoorganizacao", "title" => "Tipo de Organização", "lista" => $listTipoOrganizacao)
                            );
                            ?>
                        </li>
                        <li name="estado" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "estado", "title" => "Estado", "lista" => $listEstado)
                            );
                            ?>
                        </li>
                        <li name="regiao" class="filter-type">
                            <?PHP
                            resource_component(
                                    "Filter.php", array("id" => "regiao", "title" => "Região", "lista" => $listRegiao)
                            );
                            ?>
                        </li>
                    </ul>
                </div>
                <div class="filterbar-div filterbar-footer">
                </div>
            </div>
        </div>
        <div id="map">
            <p id="loading" style="color:white;padding-top: 90px;text-align: center">
                <i class="fa fa-map-marker"></i> 
                Carregando<br/><br/>
                <i style="text-decoration: none;font-size: 30px">Google Maps</i><br/><br/>
                Aguarde ...
                <b class="problem"></b>
            </p>
        </div> 
        <div id="marker-dialog" class="marker-dialog">
            <div class="header">
                <button class="close-btn"><i class="fa fa-chevron-up"></i> FECHAR</button>
            </div>
            <table class="overview">
                <tr>
                    <td class="value" id="name-mun">
                        %Município%(%UF%)
                    </td>
                    <td class="value" id="cod-mun">
                        %COD_MUN%
                    </td>
                </tr>  
            </table>
            <div class="notebook" id="notebook-marker-dialog">
                <div class="tabs-header">
                    <div class="tab-header selected">
                        Lista de Cursos no Município
                    </div>
                    <div class="tab-header">
                        Gráficos
                    </div>
                    <div class="tab-header">
                        Exportar
                    </div>
                </div>
                <div class="tabs">
                    <div class="tab" id="cursos-tab">
                        <table class="table-list" id="table-cursos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome (Inep)</th>
                                    <th>Instituição</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab" id="graphs-tab">
                        <ul>
                            <?PHP
                            resource_component("Graph.php", array("id" => "grau", "title" => "Grau", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "rede", "title" => "Rede", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "modalidade", "title" => "Modalidade", "type" => "bars"));
                            resource_component("Graph.php", array("id" => "natureza", "title" => "Natureza Privada", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "naturezadep", "title" => "Natureza Pública", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "nivel", "title" => "Nível", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "programa", "title" => "Programa do Curso", "type" => "bars"));
                            resource_component("Graph.php", array("id" => "tipoorganizacao", "title" => "Tipo da Organização", "type" => "sector"));
                            resource_component("Graph.php", array("id" => "estado", "title" => "Estado", "type" => "bars"));
                            ?>
                        </ul>
                    </div>
                    <div class="tab" id="export-tab">
                    </div>
                </div>
            </div>
        </div>
        <a class="logotipo" id="logotipo_unesp"><img width="100" height="36" src="images/logotipos/unesp-placeholder-mini.png" /></a>
        <a class="logotipo" id="logotipo_sbc"><img width="30" height="36" src="images/logotipos/sbc_placeholder_mini_2.png"/></a>
    </body>
</html>