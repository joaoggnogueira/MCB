<md-dialog md-theme="principal" aria-label="Formulário" style="min-width: 800px;height: calc(100% - 50px);">
    <md-toolbar md-scroll-shrink>
        <div class="md-toolbar-tools">
            <h2>Sugestão</h2>
            <span flex></span>
            <md-button class="md-icon-button" ng-click="cancel()">
                <md-icon md-svg-src="../images/material/close.svg" aria-label="Fechar janela"></md-icon>
            </md-button>
        </div>
    </md-toolbar>
    <md-dialog-content style="height: 100%;">
        <md-tabs class="md-primary" layout-fill md-center-tabs>
            <md-tab label="Edição">
                <md-content class="md-padding">
                    <md-input-container md-scroll-shrink style="height: 20px;margin: 0px;">
                        <md-checkbox ng-model="show_not_changed" aria-label="Mostrar campos inalterados">Mostrar campos inalterados</md-checkbox>
                    </md-input-container>
                    <section>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!grau.changed) && (!show_not_changed)">
                            <label class="span-label">Grau</label>
                            <span class="span-text" id="grau-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!modalidade.changed) && (!show_not_changed)">
                            <label class="span-label">Modalidade</label>
                            <span class="span-text" id="modalidade-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!nivel.changed) && (!show_not_changed)">
                            <label class="span-label">Nível</label>
                            <span class="span-text" id="nivel-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!programa.changed) && (!show_not_changed)">
                            <label class="span-label">Programa</label>
                            <span class="span-text" id="programa-bind" ></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!local_oferta.changed) && (!show_not_changed)">
                            <label class="span-label">Local da Oferta (Campus, Polo, Unidade, Centro, Unidade Acadêmica, etc ...)</label>
                            <span class="span-text" id="local_oferta-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-section-container" ng-hide="(!(vespertino.changed || matutino.changed || noturno.changed || integral.changed)) && !(!show_not_changed)">
                            <label class="span-label">Turno</label>
                            <section class="fieldset-container">
                                <fieldset class="standard span-fieldset" ng-hide="(!matutino.changed) && (!show_not_changed)">
                                    <legend>MATUTINO</legend>
                                    <span ng-if="matutino.last !== matutino.now">
                                        <md-input-container><md-checkbox ng-readonly="true" ng-model="matutino.last" aria-label="Turno Matutino"></md-checkbox></md-input-container>
                                        <md-input-container><span> -> </span></md-input-container>
                                    </span>
                                    <md-input-container><md-checkbox ng-readonly="true" ng-model="matutino.now" aria-label="Turno Matutino"></md-checkbox></md-input-container>
                                </fieldset>
                                <fieldset class="standard span-fieldset" ng-hide="(!vespertino.changed) && (!show_not_changed)">
                                    <legend>VESPERTINO</legend>
                                    <md-input-container><md-checkbox ng-readonly="true" ng-model="vespertino.last" aria-label="Turno vespertino"></md-checkbox></md-input-container>
                                    <span ng-if="vespertino.last !== vespertino.now">
                                        <md-input-container>-></md-input-container>
                                        <md-input-container><md-checkbox ng-readonly="true" ng-model="vespertino.now" aria-label="Turno vespertino"></md-checkbox></md-input-container>
                                    </span>
                                </fieldset>
                                <fieldset class="standard span-fieldset" ng-hide="(!noturno.changed) && (!show_not_changed)">
                                    <legend>NOTURNO</legend>
                                    <md-input-container><md-checkbox ng-readonly="true" ng-model="noturno.last" aria-label="Turno Noturno"></md-checkbox></md-input-container>
                                    <span ng-if="noturno.last !== noturno.now">
                                        <md-input-container><span> -> </span></md-input-container>
                                        <md-input-container><md-checkbox ng-readonly="true" ng-model="noturno.now" aria-label="Turno Noturno"></md-checkbox></md-input-container>
                                    </span>
                                </fieldset>
                                <fieldset class="standard span-fieldset" ng-hide="(!integral.changed) && (!show_not_changed)">
                                    <legend>INTEGRAL</legend>
                                    <md-input-container><md-checkbox ng-readonly="true" ng-model="integral.last" aria-label="Turno Integral"></md-checkbox></md-input-container>
                                    <span ng-if="integral.last !== integral.now">
                                        <md-input-container><span> -> </span></md-input-container>
                                        <md-input-container><md-checkbox ng-readonly="true" ng-model="integral.now" aria-label="Turno Integral"></md-checkbox></md-input-container>
                                    </span>
                                </fieldset>
                            </section>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!total_alunos.changed) && (!show_not_changed)">
                            <label class="span-label">Total de Alunos</label>
                            <span class="span-text" id="total_alunos-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!carga_horaria.changed) && (!show_not_changed)">
                            <label class="span-label">Carga Horária</label>
                            <span class="span-text" id="carga_horaria-bind"></span>
                        </md-input-container>
                    </section>
                    <section>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!ies.changed) && (!show_not_changed)">
                            <label class="span-label">Instituição</label>
                            <span class="span-text" id="ies-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!tipo_organizacao.changed) && (!show_not_changed)">
                            <label class="span-label">Tipo Organização</label>
                            <span class="span-text" id="tipo_organizacao-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!municipio.changed) && (!show_not_changed)">
                            <label class="span-label">Município</label>
                            <span class="span-text" id="municipio-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!rede.changed) && (!show_not_changed)">
                            <label class="span-label">Rede</label>
                            <span class="span-text" id="rede-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!natureza.changed) && (!show_not_changed)">
                            <label class="span-label">Natureza</label>
                            <span class="span-text" id="natureza-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!natureza_juridica.changed) && (!show_not_changed)">
                            <label class="span-label">Natureza Jurídica</label>
                            <span class="span-text" id="natureza_juridica-bind"></span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container" ng-hide="(!mantenedora.changed) && (!show_not_changed)">
                            <label class="span-label">Mantenedora (Associação, Centro, Instituto, Fundação, Sociedade, Secretaria, etc..)</label>
                            <span class="span-text" id="mantenedora-bind"></span>
                        </md-input-container>
                    </section>
                </md-content>
            </md-tab>
            <md-tab label="Sobre">
                <md-content class="md-padding">
                    <section>
                        <md-subheader class="md-hue-1 md-whiteframe-1dp" md-scroll-shrink>
                            <md-icon md-svg-icon="../images/material/overview.svg"></md-icon>
                            &nbsp;&nbsp; Contexto
                        </md-subheader>
                        <md-input-container class="md-block custom-input-container">
                            <label class="span-label">Mapa</label>
                            <span class="span-text">{{mapa}}</span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container">
                            <label class="span-label">Registro</label>
                            <span class="span-text">{{registro}}</span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container">
                            <label class="span-label">Situação</label>
                            <span class="span-text">{{situacao}}</span>
                        </md-input-container>
                        <md-input-container class="md-block custom-input-container">
                            <label class="span-label">Última Revisão</label>
                            <span class="span-text">{{revisao}}</span>
                        </md-input-container>
                    </section>
                </md-content>
            </md-tab>
            <md-tab label="Adicionais">
                <md-content class="md-padding">
                    <section>
                        <md-subheader class="md-hue-1 md-whiteframe-1dp" md-scroll-shrink>
                            &nbsp;&nbsp; Original
                        </md-subheader>
                        <div id="rich_editor-original">

                        </div>
                        <md-subheader class="md-hue-1 md-whiteframe-1dp" md-scroll-shrink>
                            &nbsp;&nbsp; Alteração
                        </md-subheader>
                        <div id="rich_editor-alteracao">

                        </div>
                    </section>
                </md-content>
            </md-tab>
        </md-tabs>

    </md-dialog-content>
    <md-dialog-actions layout="row">
        <span flex></span>
        <button class="md-raised" md-button ng-if="status === 'P'">
            Editar
        </button>
        <button class="md-raised" md-button ng-click="arquivar_sugestao();" ng-if="status === 'P'">
            Arquivar
        </button>
        <button class="md-raised" md-button ng-click="desarquivar_sugestao();" ng-if="status === 'X'">
            Desarquivar
        </button>
    </md-dialog-actions>
</md-dialog>