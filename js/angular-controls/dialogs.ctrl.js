
function ProgramaDialogController($scope, $http, $mdDialog, $mdToast, $http, term) {

    function get_selects_area_do_conhecimento(scope, http, callback) {
        http({
            method: 'POST',
            url: './request/get_selects_areas.php',
            data: JSON.stringify({})
        }).then((response) => {
            var data = response.data;
            if (data.success) {
                callback(data.data);
            } else {
                scope.showMessageDialog("Opss", data.message);
            }
        }).catch((response) => {
            scope.showMessageDialog("Opss", "Erro interno 0x4");
            console.error(response);
        });
    }

    get_selects_area_do_conhecimento($scope, $http, (data) => {
        $scope.selects = data;
        $scope.filter_area_especifica = (area_especifica) => (area_especifica.id_area_geral === $scope.area_geral);
        $scope.filter_area_detalhada = (area_detalhada) => (area_detalhada.id_area_especifica === $scope.area_especifica);
    });

    $scope.nome = term.toUpperCase();

    $scope.hide = function () {
        $mdDialog.hide();
    };

    $scope.cancel = function () {
        $mdDialog.cancel();
    };

    $scope.change_area_geral = function () {
        $scope.area_especifica = -1;
        $scope.area_detalhada = -1;
    };

    $scope.change_area_especifica = function () {
        $scope.area_detalhada = -1;
    };

    $scope.change_area_detalhada = function () {

    };

    function concat(str1, str2) {
        return (str1 + " - " + str2).toString();
    }

    $scope.submitForm = function (isValid) {
        if (isValid) {

            var data = {
                nome: $scope.nome.toUpperCase(),
                cod: $scope.codigo.toUpperCase(),
                area_detalhada: $scope.area_detalhada,
                display: concat($scope.codigo.toUpperCase(), $scope.nome.toUpperCase())
            };

            $http({
                method: 'POST',
                url: './request/find_programa.php',
                data: JSON.stringify(data)
            }).then((response) => {
                var list = response.data.data;
                if (list.length !== 0) {
                    $mdToast.show(
                            $mdToast.simple()
                            .textContent("Código ou Nome de Programa já existe")
                            .position("bottom left")
                            .hideDelay(3000)
                            );
                    return false;
                } else {
                    $mdDialog.hide(data);
                }
            }).catch((response) => {
                $mdToast.show(
                        $mdToast.simple()
                        .textContent(response)
                        .position("bottom left")
                        .hideDelay(3000)
                        );
            });
        }
    };

}

function MantenedoraDialogController($scope, $http, $mdDialog, $mdToast, term) {

    $scope.nome = term.toUpperCase();

    $scope.hide = function () {
        $mdDialog.hide();
    };

    $scope.cancel = function () {
        $mdDialog.cancel();
    };

    $scope.submitForm = function (isValid) {
        if (isValid) {
            var data = {
                nome: $scope.nome.toUpperCase(),
                cnpj: $scope.cnpj.toUpperCase()
            };

            $http({
                method: 'POST',
                url: './request/find_mantenedora.php',
                data: JSON.stringify(data)
            }).then((response) => {
                var list = response.data.data;
                if (list.length !== 0) {
                    $mdToast.show(
                            $mdToast.simple()
                            .textContent("CNPJ ou NOME já existe")
                            .position("bottom left")
                            .hideDelay(3000)
                            );
                    return false;
                } else {
                    $mdDialog.hide(data);
                }
            }).catch((response) => {
                $mdToast.show(
                        $mdToast.simple()
                        .textContent(response)
                        .position("bottom left")
                        .hideDelay(3000)
                        );
            });
        }
    };
}

function SimpleDialogController($scope, $mdToast, $mdDialog, formname, initialValue, validate, type, label) {
    $scope.formname = formname;
    $scope.nome = initialValue.toUpperCase();
    $scope.type = type;
    $scope.label = label;
    
    function submit(error) {
        if (error) {
            $mdToast.show($mdToast.simple().textContent(error).position("bottom left").hideDelay(3000));
            return false;
        } else {
            $mdDialog.hide($scope.nome.toUpperCase());
        }
    }

    $scope.submitForm = function (isValid) {
        if (isValid) {
            if (validate) {
                let error = validate($scope.nome.toUpperCase());
                console.log(error);
                if (typeof error === "object") {
                    error.then((data) => submit(data));
                } else {
                    submit(error);
                }
            } else {
                $mdDialog.hide($scope.nome.toUpperCase());
            }
        }
    };

    $scope.hide = function () {
        $mdDialog.hide();
    };

    $scope.cancel = function () {
        $mdDialog.cancel();
    };
}

function TextEditorDialogController($scope, $mdDialog, contents) {
    var quill_adicionais;
    angular.element(document).ready(function () {
        const toolbarOptions = [['bold', 'italic', 'underline', 'strike'], // toggled buttons
            ['blockquote', 'code-block'],
            [{'header': 1}, {'header': 2}], // custom button values
            [{'list': 'ordered'}, {'list': 'bullet'}],
            [{'script': 'sub'}, {'script': 'super'}], // superscript/subscript
            [{'indent': '-1'}, {'indent': '+1'}], // outdent/indent
            [{'direction': 'rtl'}], // text direction
            [{'size': ['small', false, 'large', 'huge']}], // custom dropdown
            [{'header': [1, 2, 3, 4, 5, 6, false]}],
            [{'color': []}, {'background': []}], // dropdown with defaults from theme
            [{'font': []}],
            [{'align': []}],
            ['clean']
        ];

        quill_adicionais = new Quill('#rich_editor_dialog', {
            theme: 'snow', modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Escreva aqui informações adicionais sobre o curso'
        });

        quill_adicionais.setContents(contents);
    });

    $scope.submitForm = function () {
        $mdDialog.hide(quill_adicionais.getContents());
    };

    $scope.hide = function () {
        $mdDialog.hide(false);
    };

    $scope.cancel = function () {
        $mdDialog.cancel();
    };
}
