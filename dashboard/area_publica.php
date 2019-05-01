<?PHP
include "../config/config.php";
?>
<html lang="pt-BR">
    <head>
        <title>Area Pública - MCB</title>
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
            const __AppName__ = "PublicApp";
        </script>
        <script src="<?= resource_script("angular-controls/publicApp.ctrl.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/functions.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/dialogs.ctrl.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/theme.ctrl.js") ?>"></script>
        <script src="https://apis.google.com/js/platform.js?hl=pt-br" async defer></script>
        <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

        <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
    </head>
    <body ng-app="PublicApp" ng-cloak ng-controller="OverviewCtrl" >
    <md-content id="body-content">
        <?PHP include "./include/topbar_public.php" ?>
        <md-whiteframe md-theme="principal" ng-class="{'mobile':mobile}" ng-controller="FormCtrl" id="content" class="md-whiteframe-4dp">
            <md-toolbar ng-hide="mobile">
                <div class="md-toolbar-tools">Sugestões</div>
            </md-toolbar>
            <md-content ng-if="!isUserLogged()">
                <section layout-padding layout-margin layout="column" layout-align="center center" style="height: calc(100% - 100px);">
                    <md-icon md-svg-icon="<?= resource("images/material/warning.svg") ?>" style="width: 64px; height: 64px" ></md-icon>
                    <span>Faça o acesso na sua conta Google, para ter acesso as funcionalidades</span>
                </section>
            </md-content>
            <md-content ng-if="isUserLogged()" style="overflow: auto;height: calc(100% - 64px);">
                <section>
                    <md-subheader class="md-whiteframe-1dp md-primary">
                        Sugestões Pendentes
                    </md-subheader>
                    <md-list layout-padding ng-if="sugestoes_pendentes.length === 0">
                        <md-list-item class="md-3-line">
                            <img ng-src="<?= resource("images/material/warning.svg") ?>" class="md-avatar" alt="Lista vazia">
                            <div class="md-list-item-text">
                                <h3>Lista vazia</h3>
                                <p>Escolha um curso e faça sua sugestão de edição</p>
                            </div>
                        </md-list-item>
                    </md-list>
                    <md-list layout-padding ng-if="sugestoes_pendentes.length !== 0">
                        <md-list-item class="md-3-line span-list-item" ng-repeat="sugestao in sugestoes_pendentes">
                            <button md-button ng-click="showSugestao($event, sugestao)">
                                <img ng-src="<?= resource("images/material/") ?>{{sugestao.icon}}" class="md-avatar" alt="{{sugestao.registro}}">
                                <div class="md-list-item-text">
                                    <h3>{{sugestao.registro}}</h3>
                                    <h4>{{sugestao.datetime}}</h4>
                                </div>
                            </button>
                        </md-list-item>
                    </md-list>
                </section>
                <section>
                    <md-subheader class="md-whiteframe-1dp md-primary">
                        Sugestões Aceitas
                    </md-subheader>
                    <md-list layout-padding ng-if="sugestoes_aceitas.length === 0">
                        <md-list-item class="md-3-line">
                            <img ng-src="<?= resource("images/material/warning.svg") ?>" class="md-avatar" alt="Lista vazia">
                            <div class="md-list-item-text">
                                <h3>Lista vazia</h3>
                                <p>Nenhuma sugestão foi aceita</p>
                            </div>
                        </md-list-item>
                    </md-list>
                    <md-list layout-padding ng-if="sugestoes_aceitas.length !== 0">
                        <md-list-item class="md-3-line span-list-item" ng-repeat="sugestao in sugestoes_aceitas">
                            <img ng-src="<?= resource("images/material/") ?>{{sugestao.icon}}" class="md-avatar" alt="{{sugestao.registro}}">
                            <div class="md-list-item-text">
                                <h3>{{sugestao.registro}}</h3>
                                <h4>{{sugestao.datetime}}</h4>
                            </div>
                        </md-list-item>
                    </md-list>
                </section>
                <section>
                    <md-subheader class="md-whiteframe-1dp md-primary">
                        Sugestões Recusadas
                    </md-subheader>
                    <md-list layout-padding ng-if="sugestoes_recusadas.length === 0">
                        <md-list-item class="md-3-line">
                            <img ng-src="<?= resource("images/material/warning.svg") ?>" class="md-avatar" alt="Lista vazia">
                            <div class="md-list-item-text">
                                <h3>Lista vazia</h3>
                                <p>Nenhuma sugestão foi recusada</p>
                            </div>
                        </md-list-item>
                    </md-list>
                    <md-list layout-padding ng-if="sugestoes_recusadas.length !== 0">
                        <md-list-item class="md-3-line span-list-item" ng-repeat="sugestao in sugestoes_recusadas">
                            <img ng-src="<?= resource("images/material/") ?>{{sugestao.icon}}" class="md-avatar" alt="{{sugestao.registro}}">
                            <div class="md-list-item-text">
                                <h3>{{sugestao.registro}}</h3>
                                <h4>{{sugestao.datetime}}</h4>
                            </div>
                        </md-list-item>
                    </md-list>
                </section>
                <section>
                    <md-subheader class="md-whiteframe-1dp md-primary">
                        Sugestões Arquivadas
                    </md-subheader>
                    <md-list layout-padding ng-if="sugestoes_arquivadas.length === 0">
                        <md-list-item class="md-3-line">
                            <img ng-src="<?= resource("images/material/warning.svg") ?>" class="md-avatar" alt="Lista vazia">
                            <div class="md-list-item-text">
                                <h3>Lista vazia</h3>
                                <p>Nenhuma sugestão foi arquivada por você</p>
                            </div>
                        </md-list-item>
                    </md-list>
                    <md-list layout-padding ng-if="sugestoes_arquivadas.length !== 0">
                        <md-list-item class="md-3-line span-list-item" ng-repeat="sugestao in sugestoes_arquivadas">
                            <button md-button ng-click="showSugestao($event, sugestao)">
                                <img ng-src="<?= resource("images/material/") ?>{{sugestao.icon}}" class="md-avatar" alt="{{sugestao.registro}}">
                                <div class="md-list-item-text">
                                    <h3>{{sugestao.registro}}</h3>
                                    <h4>{{sugestao.datetime}}</h4>
                                </div>
                            </button>
                        </md-list-item>
                    </md-list>
                </section>
            </md-content>
        </md-whiteframe>
    </md-content>
</body>
</html>
