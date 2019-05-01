<md-dialog md-theme="principal" aria-label="Formulário" style="min-width: 425px;">
    <form name='formDialog' ng-submit="submitForm(formDialog.$valid)">
        <md-dialog-content>
            <div class="md-toolbar-tools">
                <h2>Inserir {{ formname}}</h2>
                <span flex></span>
                <md-button class="md-icon-button" ng-click="cancel()">
                    <md-icon md-svg-src="../images/material/close.svg" aria-label="Fechar janela"></md-icon>
                </md-button>
            </div>
            <section>
                <md-input-container class="md-block custom-input-container" style="height: auto">
                    <label>{{label}}</label>
                    <input autofocus autocomplete="off" md-no-asterisk minlength="5" maxlength="50" type="{{type}}" ng-required="true" required name="nome" ng-model="nome" style="text-transform: uppercase" type="text"/>
                    <div ng-messages="formDialog.nome.$error">
                        <div ng-message="required">O campo não pode ser vazio.</div>
                        <div ng-message-exp="['maxlength','minlength']">Mínimo de 5, Máximo de 50 caracteres.</div>
                    </div>
                </md-input-container>
            </section>
        </md-dialog-content>

        <md-dialog-actions layout="row">
            <span flex></span>
            <button class="md-raised md-primary" md-button type="submit">
                OK
            </button>
        </md-dialog-actions>
    </form>
</md-dialog>