<?PHP
include "../config/config.php";
?>
<html lang="pt-BR">
    <head>
        <title>Sugestão - MCB</title>
        <meta name="viewport" content="width=device-width">
        <meta charset="UTF-8" />
        <meta name="theme-color" content="#4267b2"/>
        <meta name="Description" content="Mapa dos Cursos de Computação e Similares no Território Nacional">
        <meta name="author" content="João G. G. Nogueira">
        <meta name="keywords" content="Mapa, Ciência da Computação, Tecnologia da Informação, Visualização de Dados">
        <meta name="google-signin-client_id" content="380206327858-dirojljpoq7ro6cne781eat5oc55bi0u.apps.googleusercontent.com">

        <link rel="manifest" href="../manifest.json"/>
        <meta name="robots" content="all">
        <meta name="googlebot-news" content="noindex" />
        <link rel="shortcut icon" href="../images/icon/mcb-32.png"/>

        <link async rel="stylesheet" href="<?= resource_css("dashboard/sugestao.css") ?>" />
        <link async rel="stylesheet" href="<?= resource_css("font.css"); ?>">
        <link async rel="stylesheet" href="<?= resource_css("angular-material/angular-material.min.css") ?>" />

        <!-- Angular JS 1.x -->
        <script src="<?= resource_script("angular-material/angular.min.js") ?>"></script>
        <script src="<?= resource_script("angular-material/angular-animate.min.js") ?>"></script>
        <script src="<?= resource_script("angular-material/angular-aria.min.js") ?>"></script>
        <script src="<?= resource_script("angular-material/angular-route.min.js") ?>"></script>
        <script src="<?= resource_script("angular-material/angular-messages.min.js") ?>"></script>

        <!-- Angular Material Library -->
        <script src="<?= resource_script("angular-material/angular-material.min.js") ?>"></script>

        <!-- Angular Controller -->
        <script>
            const __AppName__ = "SugestaoApp";
        </script>
        <script src="<?= resource_script("angular-controls/functions.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/sugestao.ctrl.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/dialogs.ctrl.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/theme.ctrl.js") ?>"></script>
        <script src="https://apis.google.com/js/platform.js?hl=pt-br" async defer></script>

        <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

        <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
    </head>
    <body ng-app="SugestaoApp" ng-class="{dark:false}" ng-controller="OverviewCtrl" ng-cloak >
    <md-content id="body-content">
        <?PHP include "./include/topbar_public.php" ?>
        <md-whiteframe md-theme="principal" ng-class="{'mobile':mobile}" ng-controller="FormCtrl" id="content" class="md-whiteframe-4dp">
            <md-toolbar ng-hide="mobile">
                <div class="md-toolbar-tools">Formulário de Edição</div>
            </md-toolbar>
            <md-content>
                <md-content id="form">
                    <section>
                        <md-subheader class="md-hue-1 md-whiteframe-1dp">
                            <md-icon md-svg-icon="../images/material/overview.svg"></md-icon>
                            &nbsp;&nbsp; Contexto
                        </md-subheader>
                        <md-input-container class="md-block custom-input-container">
                            <label>Mapa</label>
                            <input ng-model="mapa" type="text" readonly/>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container">
                            <label>Registro</label>
                            <input ng-model="registro" type="text" readonly/>
                        </md-input-container>
                    </section>
                    <div ng-hide="true">
                        <section>
                            <md-subheader class="md-hue-1 md-whiteframe-1dp">
                                <md-icon md-svg-icon="../images/material/superior.svg"></md-icon>
                                &nbsp;&nbsp; Informações sobre o Curso
                            </md-subheader>
                            <md-input-container class="md-block custom-select-container">
                                <label>Grau</label>
                                <md-select ng-model="grau" id='grau'>
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option ng-repeat="item in selects.grau" ng-value="item.id">
                                        {{ (item.nome) + ((item.mapa === "reuse")?(" (aprovação pendente)"):(""))}}
                                    </md-option>
                                    <md-option value="outro" ng-click="createGrauDialog()">
                                        <a class="linkdialog">Outro ?!</a>
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-select-container">
                                <label>Modalidade</label>
                                <md-select ng-model="modalidade" id='modalidade'>
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option ng-repeat="item in selects.modalidade" ng-value="item.id">
                                        {{item.nome}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-select-container">
                                <label>Nível</label>
                                <md-select ng-model="nivel" id='nivel'>
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option style="text-transform: uppercase;" ng-repeat="item in selects.nivel" ng-value="item.id">
                                        {{item.nome}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-autocomplete-container">
                                <label>Programa</label>
                                <md-autocomplete
                                    md-no-cache="programa.noCache"
                                    md-selected-item="programa.selectedItem"
                                    md-selected-item-change="programa.selectedItemChange(item)"
                                    md-items="item in programa.querySearch(programa.searchText)"
                                    md-search-text="programa.searchText"
                                    md-item-text="item.display"
                                    md-min-length="3"
                                    placeholder="Digite o nome ou código do programa"
                                    aria-label="Pesquisar programa"
                                    id='programa'>
                                    <md-item-template>
                                        <span md-highlight-text="programa.searchText" md-highlight-flags="^i">{{item.display}}</span>
                                    </md-item-template>
                                    <md-not-found >
                                        <a ng-click="programa.createDialog(programa.searchText)">Nenhum programa cadastrado corresponde a pesquisa &nbsp;&nbsp;&nbsp; <span class="linkdialog">Cadastrar ?!</span></a>
                                    </md-not-found>
                                </md-autocomplete>
                            </md-input-container>
                            <md-input-container class="md-block custom-autocomplete-container">
                                <label>Local da Oferta (Campus, Polo, Unidade, Centro, Unidade Acadêmica, etc ...)</label>
                                <md-autocomplete
                                    id='local_oferta'
                                    md-no-cache="local_oferta.noCache"
                                    md-selected-item="local_oferta.selectedItem"
                                    md-selected-item-change="local_oferta.selectedItemChange(item)"
                                    md-items="item in local_oferta.querySearch(local_oferta.searchText)"
                                    md-search-text="local_oferta.searchText"
                                    md-item-text="item.display"
                                    md-min-length="3"
                                    placeholder="Digite o nome do local de oferta do curso"
                                    aria-label="Pesquisar local de oferta">
                                    <md-item-template>
                                        <span md-highlight-text="local_oferta.searchText" md-highlight-flags="^i">{{item.display}}</span>
                                    </md-item-template>
                                    <md-not-found>
                                        <a ng-click="local_oferta.createDialog(programa.searchText)">Nenhum local de oferta cadastrado corresponde a pesquisa. <span class="linkdialog">Cadastrar ?!</span></a>
                                    </md-not-found>
                                </md-autocomplete>
                            </md-input-container>
                            <md-input-container class="md-block custom-section-container">
                                <fieldset class="standard">
                                    <legend>Turno</legend>
                                    <md-checkbox id='matutino' ng-model="matutino" aria-label="Turno Matutino">
                                        MATUTINO
                                    </md-checkbox>
                                    <md-checkbox id='vespertino' ng-model="vespertino" aria-label="Turno vespertino">
                                        VESPERTINO
                                    </md-checkbox>
                                    <md-checkbox id='noturno' ng-model="noturno" aria-label="Turno Noturno">
                                        NOTURNO
                                    </md-checkbox>
                                    <md-checkbox id='integral' ng-model="integral" aria-label="Turno Integral">
                                        INTEGRAL
                                    </md-checkbox>
                                </fieldset>
                            </md-input-container>
                            <div class="custom-block-container">
                                <md-input-container class="md-block custom-spinner-container">
                                    <label>Total de Alunos</label>
                                    <input id='total_alunos' ng-disabled="total_de_alunos_ni" ng-model="total_de_alunos" type="number">
                                </md-input-container>
                                <md-checkbox ng-model="total_de_alunos_ni" aria-label="Total de alunos não informado">
                                    Não Informado
                                </md-checkbox>
                            </div>
                            <div class="custom-block-container">
                                <md-input-container class="md-block custom-spinner-container">
                                    <label>
                                        Carga Horária
                                    </label>
                                    <input id='carga_horaria' ng-disabled="carga_horaria_ni" ng-model="carga_horaria" type="number">
                                </md-input-container>
                                <md-checkbox ng-model="carga_horaria_ni" aria-label="Carga horária não informado">
                                    Não Informado
                                </md-checkbox>
                            </div>
                        </section>
                        <section>
                            <md-subheader class="md-hue-1 md-whiteframe-1dp">
                                <md-icon md-svg-icon="../images/material/old-school.svg"></md-icon>
                                &nbsp;&nbsp; Informações sobre a Instituição
                            </md-subheader>
                            <md-input-container class="md-block custom-autocomplete-container">
                                <label>Instituição</label>
                                <md-autocomplete
                                    md-no-cache="ies.noCache"
                                    md-selected-item="ies.selectedItem"
                                    md-selected-item-change="ies.selectedItemChange(item)"
                                    md-items="item in ies.querySearch(ies.searchText)"
                                    md-search-text="ies.searchText"
                                    md-item-text="item.display"
                                    md-min-length="3"
                                    placeholder="Digite a sigla ou nome da instituição de ensino"
                                    aria-label="Pesquisar instituição de ensino"
                                    id='instutuicao'>
                                    <md-item-template>
                                        <span md-highlight-text="ies.searchText" md-highlight-flags="^i">{{item.display}}</span>
                                    </md-item-template>
                                    <md-not-found>
                                        Nenhuma instituição de ensino cadastrada corresponde a pesquisa.
                                    </md-not-found>
                                </md-autocomplete>
                            </md-input-container>
                            <md-input-container  class="md-block custom-select-container">
                                <label>Tipo Organização</label>
                                <md-select ng-model="tipo_organizacao" id='tipo_organizacao'>
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option ng-repeat="item in selects.tipo_organizacao" ng-value="item.id">
                                        {{ (item.nome) + ((item.mapa === "reuse")?(" (aprovação pendente)"):(""))}}
                                    </md-option>
                                    <md-option value="outro" ng-click="createTipoOrganizacao()">
                                        <a class="linkdialog">Outro ?!</a>
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-autocomplete-container">
                                <label>Município</label>
                                <md-autocomplete
                                    md-no-cache="municipio.noCache"
                                    md-selected-item="municipio.selectedItem"
                                    md-selected-item-change="municipio.selectedItemChange(item)"
                                    md-items="item in municipio.querySearch(municipio.searchText)"
                                    md-search-text="municipio.searchText"
                                    md-item-text="item.display"
                                    md-min-length="3"
                                    placeholder="Digite o nome do município"
                                    aria-label="Pesquisar município"
                                    id='municipio'>
                                    <md-item-template>
                                        <span md-highlight-text="municipio.searchText" md-highlight-flags="^i">{{item.display}}</span>
                                    </md-item-template>
                                    <md-not-found>
                                        Nenhum município corresponde a pesquisa. Tente sem acentuar
                                    </md-not-found>
                                </md-autocomplete>
                            </md-input-container>
                            <md-input-container  class="md-block custom-select-container">
                                <label>Rede</label>
                                <md-select id='rede' ng-model="rede">
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option ng-repeat="item in selects.rede" ng-value="item.id">
                                        {{item.nome}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-select-container">
                                <label>Natureza</label>
                                <md-select id='natureza' ng-model="natureza">
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option ng-repeat="item in selects.natureza" ng-value="item.id">
                                        {{item.nome}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-select-container">
                                <label>Natureza Jurídica</label>
                                <md-select id='natureza_juridica' ng-model="naturezaJuridica">
                                    <md-option value="-1"><disabled>Não Informado</disabled></md-option>
                                    <md-option ng-repeat="item in selects.natureza_juridica" ng-value="item.id">
                                        {{item.nome}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container class="md-block custom-autocomplete-container">
                                <label>Mantenedora (Associação, Centro, Instituto, Fundação, Sociedade, Secretaria, etc..)</label>
                                <md-autocomplete
                                    md-no-cache="mantenedora.noCache"
                                    md-selected-item="mantenedora.selectedItem"
                                    md-selected-item-change="mantenedora.selectedItemChange(item)"
                                    md-items="item in mantenedora.querySearch(mantenedora.searchText)"
                                    md-search-text="mantenedora.searchText"
                                    md-item-text="item.display"
                                    md-min-length="3"
                                    placeholder="Digite o nome da instituição mantenedora"
                                    aria-label="Pesquisar mantenedora"
                                    id='mantenedora'>
                                    <md-item-template>
                                        <span md-highlight-text="mantenedora.searchText" md-highlight-flags="^i">{{item.display}}</span>
                                    </md-item-template>
                                    <md-not-found>
                                        <a ng-click="mantenedora.createDialog(programa.searchText)">Nenhuma instituição de mantenedora cadastrada corresponde a pesquisa. <span class="linkdialog" >Cadastrar ?!</span></a>
                                    </md-not-found>
                                </md-autocomplete>
                            </md-input-container>
                        </section>
                    </div>
                    <section>
                        <md-subheader style="margin-bottom: 0px;" class="md-hue-1 md-whiteframe-1dp">
                            <md-icon md-svg-icon="../images/material/overview.svg"></md-icon>
                            &nbsp;&nbsp; Informações Adicionais
                        </md-subheader>
                        <md-input-container style="height:auto" class="md-block custom-input-container">
                            <button md-button aria-label="Salvar" class="md-raised" ng-click="show_rich_editor();" style="margin-bottom: 25px;"> ABRIR EDITOR </button>
                            <div class="rich_editor">

                            </div>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container">
                            <label>Link para mais informações do curso</label>
                            <input type="text" ng-model="link" aria-label="Campo para Link" id="link"/>
                        </md-input-container>
                    </section>
                </md-content>
                <div class="footer-content">
                    <md-button aria-label="Descartar" class="md-raised" ng-click="descartarBtnEvent();">
                        <md-icon show-xs md-svg-icon="../images/material/delete.svg"></md-icon>
                        <span hide-xs>Descartar</span>
                    </md-button>
                    <button md-button aria-label="Salvar" class="md-raised md-primary" ng-click="submit();"> SALVAR </button>
                </div>
            </md-content>
        </md-whiteframe>
    </md-content>

</body>
</html>
