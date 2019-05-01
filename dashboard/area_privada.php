<?PHP
include "../config/config.php";
?>
<html lang="pt-BR">
    <head>
        <title>Area Privada - MCB</title>
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
            const __AppName__ = "PrivateApp";
        </script>
        <script src="<?= resource_script("angular-controls/privateApp.ctrl.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/functions.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/dialogs.ctrl.js") ?>"></script>
        <script src="<?= resource_script("angular-controls/theme.ctrl.js") ?>"></script>
        <script src="https://apis.google.com/js/platform.js?hl=pt-br" async defer></script>
    </head>
    <body ng-app="PrivateApp" ng-cloak ng-controller="OverviewCtrl" >
    <md-content id="body-content">
        <?PHP include "./include/topbar_private.php" ?>
        <md-whiteframe md-theme="private" ng-class="{'mobile':mobile}" ng-controller="FormCtrl" id="content" class="md-whiteframe-4dp">
            <md-toolbar ng-hide="mobile">
                <div class="md-toolbar-tools">Sugestões Pendentes</div>
            </md-toolbar>
            <md-content ng-if="!isUserLogged()">
                <section layout-padding layout-margin layout="column" layout-align="center center" style="height: calc(100% - 100px);">
                    <md-icon md-svg-icon="<?= resource("images/material/warning.svg") ?>" style="width: 64px; height: 64px" ></md-icon>
                    <span>Faça o acesso na sua conta Google, para ter acesso as funcionalidades</span>
                </section>
            </md-content>
            <md-content ng-if="isUserLogged()">
                <md-content ng-if="isUserLogged()">
                    
                </md-content>
            </md-content>
        </md-whiteframe>
    </md-content>
</body>
</html>
