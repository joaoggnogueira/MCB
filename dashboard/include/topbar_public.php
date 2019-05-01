<md-toolbar ng-controller="TopbarCtrl" md-theme="topbar" md-whiteframe="4" style="z-index:2;">
    <div class="md-toolbar-tools">
        <h2>Área Pública</h2>
        <h2 flex></h2>
        <md-menu ng-hide="!signedin">
            <md-button ng-click="$mdMenu.open($event)" class="card-profile md-whiteframe-1dp">
                <md-icon style="background-image: url({{profile_compiled.image}});" class="card-profile-image"></md-icon>
                <span hide-xs class="md-hue-1">{{profile_compiled.name}}</span>
                <md-icon md-menu-origin md-svg-icon="<?= resource("images/material/arrow_drop_down.svg") ?>"></md-icon>
            </md-button>
            <md-menu-content width="4">
                <md-card md-theme="principal">
                    <md-card-title style="padding: 8px 16px 16px;">
                        <md-card-title-text>
                            <span class="md-headline">{{profile_compiled.name}}</span>
                            <span>Credenciais de {{profile_compiled.credentials}}</span>
                        </md-card-title-text>
                    </md-card-title>
                    <img ng-src="{{profile_compiled.image}}" class="md-card-image">
                </md-card>
                <md-menu-item style="border-top: 1px solid #DDD;">
                    <md-button ng-click="logoff()">
                        <div layout="row" flex>
                            <md-icon md-svg-icon="<?= resource("images/material/logoff.svg") ?>" ></md-icon>
                            Encerrar Sessão
                        </div>
                    </md-button>
                </md-menu-item>
            </md-menu-content>
        </md-menu>
        <div ng-hide="signedin" class="g-signin2" data-onsuccess="onSignIn"></div>
    </div>
    <script src="<?= resource_script("angular-controls/topbar.ctrl.js") ?>"></script>
</md-toolbar>