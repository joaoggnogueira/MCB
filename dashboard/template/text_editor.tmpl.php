<style>
    #rich_editor_dialog, #rich_editor_dialog .ql-editor{
        height: 370px;
    }
    
</style>
<md-dialog md-theme="principal" aria-label="Editar texto" style="min-width: 425px;">
    <md-toolbar md-scroll-shrink>
        <div class="md-toolbar-tools">
            <h2>Edição de Texto</h2>
            <span flex></span>
            <md-button class="md-icon-button" ng-click="cancel()">
                <md-icon md-svg-src="../images/material/close.svg" aria-label="Fechar janela"></md-icon>
            </md-button>
        </div>
    </md-toolbar>
    <form name='formDialog' ng-submit="submitForm(formDialog.$valid)">
        <md-dialog-content>
            <section>
                <div id="rich_editor_dialog">
                    Aguarde
                </div>
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