/* Do dialogs.ctrl.js */
/* global ProgramaDialogController, MantenedoraDialogController, SimpleDialogController */

/* Do sugestao.php */
/* global __AppName__ */

(function () {

    function create_autocomplete_data() {
        const ref = {
            noCache: true,
            selectedItem: {
                id: 1,
                display: "teste"
            },
            value: 1,
            reuse: false,
            querySearch: (text) => {
                return [
                    {id: 1, display: text + "1"},
                    {id: 2, display: text + "2"},
                    {id: 3, display: text + "3"}
                ];
            },
            selectedItemChange: (item) => {
                if (typeof item === "object" && item !== null) {
                    console.log(item);
                    ref.value = item.id;
                    ref.reuse = (item.mapa === "reuse");
                } else if (item === undefined) {
                    ref.selectedItem = null;
                    ref.searchText = "";
                    ref.value = -1;
                }
            },
            searchText: "",
            display: ""

        };
        return ref;
    }

    //REQUESTS

    function check_password(mdDialog, http, scope) {
        var validate = (text) => {
            return http({
                method: 'POST',
                url: './request/check_password.php',
                data: JSON.stringify({password: text, id: scope.id_curso})
            }).then((response) => {
                if (response.data.success) {
                    if (response.data.data.access_granted) {
                        return false;
                    } else {
                        return "Senha incorreta";
                    }
                } else {
                    return "Erro ao verificar senha";
                }
            }).catch((response) => {
                console.error(response);
            });
        };
        return mdDialog.show({
            locals: {formname: 'senha', initialValue: "", validate: validate, type: "password", label: "Digite abaixo a senha de acesso ao curso"},
            controller: SimpleDialogController,
            templateUrl: 'template/form_generic.tmpl.php',
            parent: angular.element(document.body),
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                return answer;
            } else {
                return false;
            }
        }, function () {
            return false;
        });
    }

    function get_curso_data(scope, http, mdDialog, callback) {
        check_password(mdDialog, http, scope).then((password) => {
            if (password) {
                scope.password = password;
                http({
                    method: 'POST',
                    url: './request/get_curso.php',
                    data: JSON.stringify({id: scope.id_curso, password: scope.password})
                }).then((response) => {
                    var data = response.data;
                    if (data.success) {
                        callback(data.data);
                    } else {
                        scope.showMessageDialog("Opss", data.message);
                    }
                }).catch((response) => {
                    scope.showMessageDialog("Opss", "Erro interno 0x1");
                    console.error(response);
                });
            } else {
                scope.$parent.showConfirmDialog("É necessário uma senha", "Deseja cancelar e encerrar a sessão?", (ok) => {
                    if (ok) {
                        scope.showMessageDialog("Sessão encerrada", "", false, () => {
                            window.location.href = "../app.php?mapa=2";
                        });
                        
                    } else {
                        get_curso_data(scope, http, mdDialog, callback);
                    }
                });
            }
        });
    }

    function get_selects(scope, http, callback) {
        http({
            method: 'POST',
            url: './request/get_selects.php',
            data: JSON.stringify({id_mapa: scope.mapaId})
        }).then((response) => {
            var data = response.data;
            if (data.success) {
                callback(data.data);
            } else {
                scope.showMessageDialog("Opss", data.message);
            }
        }).catch((response) => {
            scope.showMessageDialog("Opss", "Erro interno 0x2");
            console.error(response);
        });
    }

    function search(term, table, http, scope, q) {
        return http({
            method: 'POST',
            url: './request/get_search.php',
            data: JSON.stringify({term: term, table: table})
        }).then((response) => {
            var data = response.data;
            if (data.success) {
                return data.data;
            } else {
                scope.showSimpleToast("Opss, Erro ao buscar dados");
            }
        }).catch((response) => {
            scope.showMessageDialog("Opss, Erro interno 0x3");
            console.error(response);
        });
    }

    //AUTCOMPLETE
    function search_programa(term, http, scope) {
        return search(term, "programa", http, scope);
    }

    function search_local_de_oferta(term, http, scope) {
        return search(term, "local_de_oferta", http, scope);
    }

    function search_instituicao(term, http, scope) {
        return search(term, "instituicao", http, scope);
    }

    function search_municipio(term, http, scope) {
        return search(term, "municipio", http, scope);
    }

    function search_mantenedora(term, http, scope) {
        return search(term, "mantenedora", http, scope);
    }

    function createProgramaDialog(ev, scope, mdDialog) {

        mdDialog.show({
            locals: {term: scope.programa.searchText},
            controller: ProgramaDialogController,
            templateUrl: 'template/form_programa.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                scope.$parent.showSimpleToast(`Programa ${answer.nome} adicionado`);
                scope.programa.searchText = answer.nome;
                scope.novoPrograma = answer;
                scope.programa.selectedItem = {id: 'novo', display: answer.nome};
            } else {
                scope.programa.selectedItem = null;
            }
        }, function () {
            scope.programa.selectedItem = null;
        });
        scope.programa.searchText = "";
    }

    function createMantenedoraDialog(ev, scope, mdDialog) {
        mdDialog.show({
            locals: {term: scope.mantenedora.searchText},
            controller: MantenedoraDialogController,
            templateUrl: 'template/form_mantenedora.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                scope.$parent.showSimpleToast(`Mantenedora '${answer.nome}' adicionado`);
                scope.mantenedora.searchText = (answer.nome);
                scope.novoMantenedora = answer;
                scope.mantenedora.selectedItem = {id: 'novo', display: answer.nome};
            } else {
                scope.mantenedora.selectedItem = null;
            }
        }, function () {
            scope.mantenedora.selectedItem = null;
        });
        scope.mantenedora.searchText = "";
    }

    function createLocalDeOfertaDialog(ev, scope, mdDialog, http) {

        var validate = (text) => {
            return http({
                method: 'POST',
                url: './request/find_local_de_oferta.php',
                data: JSON.stringify({nome: text})
            }).then((response) => {
                if (response.data.success) {
                    var list = response.data.data;
                    if (list.length !== 0) {
                        return "Local de Oferta já existe";
                    } else {
                        return false;
                    }
                } else {
                    alert("Error ao validar dados");
                    return false;
                }
            }).catch((response) => {
                console.error(response);
            });
        };

        mdDialog.show({
            locals: {formname: 'Local de Oferta', initialValue: scope.local_oferta.searchText, validate: validate},
            controller: SimpleDialogController,
            templateUrl: 'template/form_generic.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                scope.$parent.showSimpleToast(`Local de Oferta '${answer}' adicionado`);
                scope.local_oferta.searchText = (answer);
                scope.novoLocalDeOferta = answer;
                scope.local_oferta.selectedItem = {id: 'novo', display: answer};
            } else {
                scope.local_oferta.selectedItem = null;
            }
        }, function () {
            scope.local_oferta.selectedItem = null;
        });
        scope.local_oferta.searchText = "";
    }

    function createTipoOrganizacao(ev, scope, mdDialog) {

        var validate = (text) => {
            if (scope.selects.tipo_organizacao.find((item) => item.nome.toUpperCase() === text)) {
                return `O tipo de organização '${ text }' já existe`;
            }
            return false;
        };

        mdDialog.show({
            locals: {formname: 'Tipo de Organização', initialValue: "", validate: validate, type: "text", label: "Digite abaixo o nome do Tipo de Organização"},
            controller: SimpleDialogController,
            templateUrl: 'template/form_generic.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                scope.$parent.showSimpleToast(`Tipo de Organização '${answer}' adicionado`);
                var newlist = scope.selects.tipo_organizacao.filter(function (item) {
                    return item.id !== "novo";
                });
                scope.novoTipoOrganizacao = answer;
                newlist.push({id: "novo", nome: answer});
                scope.selects.tipo_organizacao = newlist;
                scope.tipo_organizacao = "novo";
            } else {
                scope.tipo_organizacao = -1;
            }
        }, function () {
            scope.tipo_organizacao = -1;
        });
    }

    function createGrauDialog(ev, scope, mdDialog) {

        var validate = (text) => {
            if (scope.selects.grau.find((item) => item.nome.toUpperCase() === text)) {
                return `O grau de ensino '${ text }' já existe`;
            }
            return false;
        };

        mdDialog.show({
            locals: {formname: 'Grau Acadêmico', initialValue: "", validate: validate, type: "text", label: "Digite abaixo o nome do Grau Acadêmico"},
            controller: SimpleDialogController,
            templateUrl: 'template/form_generic.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                scope.$parent.showSimpleToast(`Grau Acadêmico '${answer}' adicionado`);
                var newlist = scope.selects.grau.filter(function (item) {
                    return item.id !== "novo";
                });

                scope.novoGrau = answer;
                newlist.push({id: "novo", nome: answer});
                scope.selects.grau = newlist;
                scope.grau = "novo";
            } else {
                scope.grau = -1;
            }
        }, function () {
            scope.grau = -1;
        });
    }

    function createRichTextEditorDialog(ev, scope, mdDialog) {
        mdDialog.show({
            locals: {contents: scope.quill_adicionais.getContents()},
            controller: TextEditorDialogController,
            templateUrl: 'template/text_editor.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        }).then(function (answer) {
            if (answer) {
                scope.quill_adicionais.setContents(answer);
            }
        }, function () {
        });
    }

    angular.module(__AppName__, ['ngMaterial', 'ngMessages'])
            .controller('FormCtrl', function ($scope, $window, $scope, $http, $mdDialog) {

                var quill_adicionais = new Quill('.rich_editor', {
                    readOnly: true,
                    placeholder: 'Escreva aqui informações adicionais sobre o curso'
                });

                $scope.quill_adicionais = quill_adicionais;
                $scope.show_rich_editor = (ev) => createRichTextEditorDialog(ev, $scope, $mdDialog);

                $scope.id_curso = get_url_param("id_curso");

                if ($scope.id_curso === null) {
                    $scope.$parent.showMessageDialog("Opss", "Selecione um curso primeiro para editá-lo");
                    //redirect here
                } else {
                    get_curso_data($scope, $http, $mdDialog, (data_curso) => {
                        $scope.mapaId = data_curso.mapaId;
                        $scope.mapa = data_curso.mapa;
                        $scope.registro = data_curso.nome;
                        $scope.link = data_curso.link;

                        $scope.total_de_alunos = parseInt(data_curso.total_de_alunos);
                        $scope.carga_horaria = parseInt(data_curso.carga_horaria);

                        $scope.total_de_alunos_ni = ($scope.total_de_alunos === 0);
                        $scope.carga_horaria_ni = ($scope.carga_horaria === 0);

                        if (data_curso.adicionais) {
                            var a = document.createElement("div");
                            a.innerHTML = data_curso.adicionais;
                            quill_adicionais.setContents(JSON.parse(a.textContent.replace(/\n/g, "\\n")));
                        }

                        if (data_curso.id_programa && data_curso.nome_programa) {
                            $scope.programa.searchText = (data_curso.nome_programa);
                            $scope.programa.selectedItem = {
                                id: data_curso.id_programa,
                                display: data_curso.nome_programa
                            };
                        } else {
                            $scope.programa.selectedItem = null;
                        }

                        $scope.programa.querySearch = (term) => search_programa(term, $http, $scope);
                        $scope.programa.createDialog = (ev) => createProgramaDialog(ev, $scope, $mdDialog);

                        if (data_curso.id_local_de_oferta !== "-1" && data_curso.nome_local_oferta) {
                            $scope.local_oferta.searchText = (data_curso.nome_local_oferta);
                            $scope.local_oferta.selectedItem = {
                                id: data_curso.id_local_de_oferta,
                                display: data_curso.nome_local_oferta
                            };
                        } else {
                            $scope.local_oferta.selectedItem = null;
                        }

                        $scope.local_oferta.querySearch = (term) => search_local_de_oferta(term, $http, $scope);
                        $scope.local_oferta.createDialog = (ev) => createLocalDeOfertaDialog(ev, $scope, $mdDialog, $http);

                        if (data_curso.id_instituicao !== "-1" && data_curso.nome_ies) {
                            $scope.ies.searchText = data_curso.nome_ies;
                            $scope.ies.selectedItem = {
                                id: data_curso.id_instituicao,
                                display: data_curso.nome_ies
                            };
                        } else {
                            $scope.ies.selectedItem = null;
                        }

                        $scope.ies.querySearch = (term) => search_instituicao(term, $http, $scope);

                        if (data_curso.cod_municipio !== "-1") {
                            $scope.municipio.searchText = data_curso.nome_municipio;
                            $scope.municipio.selectedItem = {
                                id: data_curso.cod_municipio,
                                display: data_curso.nome_municipio
                            };
                        } else {
                            $scope.municipio.selectedItem = null;
                        }

                        $scope.municipio.querySearch = (term) => search_municipio(term, $http, $scope);

                        if (data_curso.id_mantenedora !== "-1" && data_curso.nome_mantenedora) {
                            $scope.mantenedora.searchText = data_curso.nome_mantenedora;
                            $scope.mantenedora.selectedItem = {
                                id: data_curso.id_mantenedora,
                                display: data_curso.nome_mantenedora
                            };
                        } else {
                            $scope.mantenedora.selectedItem = null;
                        }

                        $scope.mantenedora.querySearch = (term) => search_mantenedora(term, $http, $scope);
                        $scope.mantenedora.createDialog = (ev) => createMantenedoraDialog(ev, $scope, $mdDialog);

                        $scope.matutino = data_curso.matutino;
                        $scope.vespertino = data_curso.vespertino;
                        $scope.noturno = data_curso.noturno;
                        $scope.integral = data_curso.integral;
                        get_selects($scope, $http, (data_selects) => {
                            $scope.selects = data_selects;

                            $scope.grau = data_curso.id_grau;
                            $scope.createGrauDialog = (ev) => createGrauDialog(ev, $scope, $mdDialog);

                            $scope.modalidade = data_curso.id_modalidade;
                            $scope.nivel = data_curso.id_nivel;
                            $scope.tipo_organizacao = data_curso.id_tipo_organizacao;
                            $scope.createTipoOrganizacao = (ev) => createTipoOrganizacao(ev, $scope, $mdDialog);

                            $scope.rede = data_curso.id_rede;
                            $scope.natureza = data_curso.id_natureza;
                            $scope.naturezaJuridica = data_curso.id_natureza_departamento;
                        });
                    });
                }

                window.dumpScope = () => {
                    return $scope;
                };

                $scope.programa = create_autocomplete_data("programa");
                $scope.local_oferta = create_autocomplete_data("local de oferta");
                $scope.ies = create_autocomplete_data("ies");
                $scope.mantenedora = create_autocomplete_data("mantenedora");
                $scope.municipio = create_autocomplete_data("municipio");

                function resize_event() {
                    $scope.mobile = ($window.innerWidth < 700);
                }

                resize_event();
                angular.element($window).bind('resize', resize_event);

                $scope.descartarBtnEvent = () => {
                    $scope.$parent.showConfirmDialog("Descartar sugestão", "Deseja descartar alteração e sair?", (ok) => {
                        if (ok) {
                            window.location.href = "../app.php?mapa=2";
                        }
                    });
                };
                $scope.submit = () => {

                    let formdata = {id: $scope.id_curso, password: $scope.password};
                    formdata.contents = JSON.stringify($scope.quill_adicionais.getContents());
                    formdata.link = $scope.link;

                    $http({
                        method: 'POST',
                        url: './request/push_adicional.php',
                        data: JSON.stringify(formdata)
                    }).then((response) => {
                        var data = response.data;
                        if (data.success) {
                            $scope.$parent.showMessageDialog("Alteração enviada com sucesso!", "", true, () => {
                                window.location.href = "../app.php?mapa=2";
                            });
                        } else {
                            $scope.$parent.showSimpleToast("✖ Falha ao cadastrar");
                        }
                    }).catch((response) => {
                        $scope.$parent.showMessageDialog("✖ Opss, Erro interno 0x1");
                        console.error(response);
                    });


//                    if (!topbarCtrl.isUserLogged()) {
//                        $scope.$parent.showMessageDialog("Faça o acesso a sua conta google", "Para identificar o autor da sugestão é necessário realizar acesso em uma conta google");
//                    } else {
//                        let formdata = {
//                            id_curso: $scope.id_curso,
//                            id_usergoogle: topbarCtrl.getUser().id,
//                            id_mapa: $scope.mapaId,
//                            data: {
//                                modalidade: $scope.modalidade,
//                                grau: $scope.grau.split("_").pop(),
//                                nivel: $scope.nivel,
//                                matutino: $scope.matutino,
//                                vespertino: $scope.vespertino,
//                                noturno: $scope.noturno,
//                                integral: $scope.integral,
//                                total_de_alunos_ni: $scope.total_de_alunos_ni,
//                                carga_horaria_ni: $scope.carga_horaria_ni,
//                                total_de_alunos: $scope.total_de_alunos,
//                                carga_horaria: $scope.carga_horaria,
//                                tipo_organizacao: $scope.tipo_organizacao.split("_").pop(),
//                                rede: $scope.rede,
//                                natureza: $scope.natureza,
//                                naturezaJuridica: $scope.naturezaJuridica,
//                                //AUTOCOMPLETE DATA
//                                ies: $scope.ies.value,
//                                programa: $scope.programa.value,
//                                municipio: $scope.municipio.value,
//                                local_de_oferta: $scope.local_oferta.value,
//                                mantenedora: $scope.mantenedora.value,
//                                adicional: $scope.quill_adicionais.getContents()
//                            },
//                            novo_data: {
//                                grau: $scope.novoGrau,
//                                tipo_organizacao: $scope.novoTipoOrganizacao,
//                                local_de_oferta: $scope.novoLocalDeOferta,
//                                programa: $scope.novoPrograma,
//                                mantenedora: $scope.novoMantenedora
//                            },
//                            reuse_in: {
//                                grau: $scope.grau.startsWith("reuse"),
//                                tipo_organizacao: $scope.tipo_organizacao.startsWith("reuse"),
//                                local_de_oferta: $scope.local_oferta.reuse,
//                                programa: $scope.programa.reuse,
//                                mantenedora: $scope.mantenedora.reuse
//                            }
//                        };
//
//                        $http({
//                            method: 'POST',
//                            url: './request/push_sugestao.php',
//                            data: JSON.stringify(formdata)
//                        }).then((response) => {
//                            var data = response.data;
//                            if (data.success) {
//                                $scope.$parent.showMessageDialog("Sugestão enviada com sucesso!", "Ela será analisada, e os dados serão atualizados em breve");
//                                window.location.href = "./area_publica.php";
//                            } else {
//                                $scope.$parent.showSimpleToast("✖ Falha ao cadastrar");
//                            }
//                        }).catch((response) => {
//                            $scope.$parent.showMessageDialog("✖ Opss, Erro interno 0x1");
//                            console.error(response);
//                        });
//
//                    }
                };

            }).controller('OverviewCtrl', function ($scope, $mdDialog, $mdToast, $log) {

        append_dialogs_methods($scope, $mdDialog, $mdToast, $log);
    });


})();