<md-dialog  md-theme="principal" aria-label="Inserir programa" style="min-width: 425px;">
    <form name='formDialog' ng-submit="submitForm(formDialog.$valid)">
        <md-dialog-content>
            <div class="md-toolbar-tools">
                <h2>Inserir programa</h2>
                <span flex></span>
                <md-button class="md-icon-button" ng-click="cancel()">
                    <md-icon md-svg-src="../images/material/close.svg" aria-label="Fechar janela"></md-icon>
                </md-button>
            </div>
            <section>
                <md-input-container class="md-block custom-input-container" style="height: auto">
                    <label>Código</label>
                    <input autocomplete="off" md-no-asterisk minlength="6" maxlength="12" ng-required="true" required style="text-transform: uppercase" placeholder="Ex: 481C01" name="codigo" ng-model="codigo" type="text"/>
                    <div ng-messages="formDialog.codigo.$error">
                        <div ng-message="required">O campo não pode ser vazio.</div>
                        <div ng-message-exp="['maxlength','minlength']">Mínimo de 6, Máximo de 12 caracteres.</div>
                    </div>
                </md-input-container>
                <md-input-container class="md-block custom-input-container" style="height: auto">
                    <label>Nome</label>
                    <input autocomplete="off" md-no-asterisk minlength="6" maxlength="50" ng-required="true" required style="text-transform: uppercase" placeholder="Ex: Ciência da computação" name="nome" ng-model="nome" type="text"/>
                    <div ng-messages="formDialog.nome.$error">
                        <div ng-message="required">O campo não pode ser vazio.</div>
                        <div ng-message-exp="['maxlength','minlength']">Mínimo de 6, Máximo de 50 caracteres.</div>
                    </div>
                </md-input-container>
                <md-input-container class="md-block custom-select-container">
                    <label>Área Geral do Conhecimento</label>
                    <md-select ng-change="change_area_geral(area_geral);" ng-model="area_geral">
                        <md-option value="-1" ng-selected="true" ng-disabled="true"><disabled>Não Informado</disabled></md-option>
                        <md-option ng-repeat="item in selects.area_geral" ng-value="item.id">
                            {{item.nome}}
                        </md-option>
                    </md-select>
                </md-input-container>
                <md-input-container class="md-block custom-select-container">
                    <label>Área Especifica do Conhecimento</label>
                    <md-select ng-change="change_area_especifica(area_especifica);" ng-model="area_especifica" ng-disabled="(area_geral == -1)">
                        <md-option value="-1" ng-selected="true"  ng-disabled="true"><disabled>Não Informado</disabled></md-option>
                        <md-option ng-repeat="item in selects.area_especifica| filter:filter_area_especifica" ng-value="item.id">
                            {{item.nome}}
                        </md-option>
                    </md-select>
                </md-input-container>
                <md-input-container class="md-block custom-select-container" style="height: auto">
                    <label>Área Detalhada</label>
                    <md-select required ng-change="change_area_detalhada(area_detalhada);" name="area_detalhada" ng-model="area_detalhada" ng-disabled="(area_especifica == -1)">
                        <md-option ng-selected="true" disabled ng-disabled="true"><disabled>Não Informado</disabled></md-option>
                        <md-option ng-repeat="item in selects.area_detalhada| filter:filter_area_detalhada" ng-value="item.id">
                            {{item.nome}}
                        </md-option>
                    </md-select>
                    <div ng-messages="formDialog.area_detalhada.$error">
                        <div ng-message="required">Selecione a área detalhada.</div>
                    </div>
                </md-input-container>
            </section>
        </md-dialog-content>

        <md-dialog-actions layout="row">
            <span flex></span>
            <button class="md-raised md-primary" md-button type="submit">
                Cadastrar
            </button>
        </md-dialog-actions>
    </form>
</md-dialog>