<md-dialog  md-theme="principal" aria-label="Inserir programa" style="min-width: 425px;">
    <form name='formDialog' ng-submit="submitForm(formDialog.$valid)">
        <md-dialog-content>
            <section>
                <div class="md-toolbar-tools">
                    <h2>Inserir Mantenedora</h2>
                    <span flex></span>
                    <md-button class="md-icon-button" ng-click="cancel()">
                        <md-icon md-svg-src="../images/material/close.svg" aria-label="Fechar janela"></md-icon>
                    </md-button>
                </div>
                <md-input-container class="md-block custom-input-container" style="height: auto">
                    <label>CNPJ</label>
                    <input name="cnpj" required md-no-asterisk style="text-transform: uppercase" ng-pattern="/^([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})$/" placeholder="Ex: 11002650000300" ng-model="cnpj" type="text"/>
                    <div ng-messages="formDialog.cnpj.$error">
                        <div ng-message="required">O campo não pode ser vazio.</div>
                        <div ng-message="pattern">Formato inválido</div>
                    </div>
                </md-input-container>
                <md-input-container class="md-block custom-input-container" style="height: auto">
                    <label>Nome</label>
                    <input name="nome" required md-no-asterisk minlength="10" maxlength="110" style="text-transform: uppercase" placeholder="Ex: ASSOCIAÇÃO PRO ENSINO SUPERIOR EM NOVO HAMBURGO" ng-model="nome" type="text"/>
                    <div ng-messages="formDialog.nome.$error">
                        <div ng-message="required">O campo não pode ser vazio.</div>
                        <div ng-message-exp="['maxlength','minlength']">Mínimo de 10, Máximo de 110 caracteres.</div>
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