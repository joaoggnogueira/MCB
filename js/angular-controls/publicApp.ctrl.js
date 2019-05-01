/* Do sugestao.php */
/* global __AppName__, bind_text */

(function () {

    function get_sugestoes(scope, http, callback) {
        http({
            method: 'POST',
            url: './request/list_sugestoes_public.php',
            data: JSON.stringify({})
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

    function get_curso_detail(scope, http, callback) {
        http({
            method: 'POST',
            url: './request/get_sugestao_detail.php',
            data: JSON.stringify({id: scope.id})
        }).then((response) => {
            var data = response.data;
            if (data.success) {
                callback(data.data);
            } else {
                alert(data.message);
            }
        }).catch((response) => {
            alert("Error");
            console.error(response);
        });
    }

    function arquivar_sugestao(scope, http) {
        return http({
            method: 'POST',
            url: './request/arquivar_sugestao.php',
            data: JSON.stringify({id: scope.id})
        }).then((response) => {
            var data = response.data;
            if (data.success) {
                return (data.data);
            } else {
                alert(data.message);
            }
        }).catch((response) => {
            alert("Error");
            console.error(response);
        });
    }

    function desarquivar_sugestao(scope, http) {
        return http({
            method: 'POST',
            url: './request/desarquivar_sugestao.php',
            data: JSON.stringify({id: scope.id})
        }).then((response) => {
            var data = response.data;
            if (data.success) {
                return (data.data);
            } else {
                alert(data.message);
            }
        }).catch((response) => {
            alert("Error");
            console.error(response);
        });
    }

    function sugestaoIntToString(atualValue, prevValue) {
        if (atualValue == prevValue) {
            return {html: `${atualValue} <span class='tag inalterado'>inalterado</span>`, changed: false};
        } else {
            return {html: `<overline class='red'>${prevValue}</overline> <b>${atualValue}</b>`, changed: true};
        }
    }

    function sugestaoSelectToString(data, prev, mapaId) {

        let atualMapaId = data.mapa;
        let atualNovo = data.novo;
        let atual = data.value;

        let mapa_complement = "";
        if (!isNull(atualMapaId) && atualMapaId != mapaId) {
            //mapa_complement = `<span class='tag mapa'>mapa ${atualMapaId}</span>`;
        }
        let novo_complement = "";
        if (atualNovo) {
            novo_complement = "<span class='tag novo'>novo</span>";
        }

        if (isNull(atual) && isNull(prev)) {
            return {html: `<i>NÃO INFORMADO</i> <span class='tag inalterado'>inalterado</span>`, changed: false};
        } else if (isNull(prev) && !isNull(atual)) {
            return {html: `<overline class='red'>NÃO INFORMADO</overline> ${atual} ${ mapa_complement } ${novo_complement}`, changed: true};
        } else if (!isNull(prev) && isNull(atual)) {
            return {html: `<overline class='red'>${prev}</overline> <i>NÃO INFORMADO</i>`, changed: true};
        } else if (prev !== atual || mapa_complement !== "") {
            return {html: `<overline class='red'>${prev}</overline> ${atual} ${ mapa_complement } ${novo_complement}`, changed: true};
        } else {
            return {html: `${atual} <span class='tag inalterado'>inalterado</span>`, changed: false};
        }
    }

    function SugestaoDialogController($scope, $http, $mdDialog, sugestao, parentScope) {
        $scope.id = sugestao.id;
        $scope.status = sugestao.status;
        $scope.mapa = "AGUARDE";
        $scope.registro = "AGUARDE";

        switch (sugestao.status) {
            case "A":
                $scope.situacao = "Sugestão aceita pelos administradores";
                break;
            case "P":
                $scope.situacao = "Sugestão ainda precisa ser analisada pelos administradores";
                break;
            case "N":
                $scope.situacao = "Sugestão recusada pelos administradores";
                break;
            case "X":
                $scope.situacao = "Sugestão arquivada";
                break;
            case "F":
                $scope.situacao = "Versão original pelos administradores";
                break;
        }



        get_curso_detail($scope, $http, (data) => {

            $scope.mapa = data.mapa;
            $scope.registro = data.nome;
            $scope.revisao = convert_datetime_to_string(data.data_criacao, data.hora_criacao);

            let atual = data.atual_edicao;
            let prev = data.prev_edicao;

            let convert = (atual, prev) => sugestaoSelectToString(atual, prev, data.mapaId);

            $scope.grau = convert(atual.grau, prev.grau);
            $scope.modalidade = convert(atual.modalidade, prev.modalidade);
            $scope.nivel = convert(atual.nivel, prev.nivel);
            $scope.programa = convert(atual.programa, prev.programa);
            $scope.local_oferta = convert(atual.local_oferta, prev.local_oferta);

            $scope.matutino = {last: (prev.matutino !== "0"), now: (atual.matutino.value !== "0")};
            $scope.vespertino = {last: (prev.vespertino !== "0"), now: (atual.vespertino.value !== "0")};
            $scope.noturno = {last: (prev.noturno !== "0"), now: (atual.noturno.value !== "0")};
            $scope.integral = {last: (prev.integral !== "0"), now: (atual.integral.value !== "0")};

            $scope.matutino.changed = ($scope.matutino.last !== $scope.matutino.now);
            $scope.vespertino.changed = ($scope.vespertino.last !== $scope.vespertino.now);
            $scope.noturno.changed = ($scope.noturno.last !== $scope.noturno.now);
            $scope.integral.changed = ($scope.integral.last !== $scope.integral.now);

            $scope.total_alunos = sugestaoIntToString(atual.total_de_alunos.value, prev.total_de_alunos);
            $scope.carga_horaria = sugestaoIntToString(atual.carga_horaria.value, prev.carga_horaria);

            $scope.ies = convert(atual.ies, prev.ies);
            $scope.tipo_organizacao = convert(atual.tipo_organizacao, prev.tipo_organizacao);
            $scope.municipio = convert(atual.municipio, prev.municipio);

            $scope.rede = convert(atual.rede, prev.rede);
            $scope.natureza = convert(atual.natureza, prev.natureza);
            $scope.natureza_juridica = convert(atual.naturezaJuridica, prev.naturezaJuridica);
            $scope.mantenedora = convert(atual.mantenedora, prev.mantenedora);

            const to_json = (string) => {
                if (string) {
                    return JSON.parse(string.replace(/\n/g, "\\n"));
                }
                return {"ops": [{"attributes": {"underline": true, "color": "#bbbbbb", "italic": true}, "insert": "Sem informações"}, {"attributes": {"header": 3}, "insert": "\n"}]};
            };
            const adicional_original = to_json(prev.adicional);
            const adicional_alteracao = to_json(atual.adicional);

            (new Quill('#rich_editor-original', {readOnly: true})).setContents(adicional_original);
            (new Quill('#rich_editor-alteracao', {readOnly: true})).setContents(adicional_alteracao);

            bind_text("grau-bind", $scope.grau.html);
            bind_text("modalidade-bind", $scope.modalidade.html);
            bind_text("nivel-bind", $scope.nivel.html);
            bind_text("programa-bind", $scope.programa.html);
            bind_text("local_oferta-bind", $scope.local_oferta.html);
            bind_text("total_alunos-bind", $scope.total_alunos.html);
            bind_text("carga_horaria-bind", $scope.carga_horaria.html);
            bind_text("ies-bind", $scope.ies.html);
            bind_text("tipo_organizacao-bind", $scope.tipo_organizacao.html);
            bind_text("municipio-bind", $scope.municipio.html);
            bind_text("rede-bind", $scope.rede.html);
            bind_text("natureza-bind", $scope.natureza.html);
            bind_text("natureza_juridica-bind", $scope.natureza_juridica.html);
            bind_text("mantenedora-bind", $scope.mantenedora.html);

        });

        $scope.arquivar_sugestao = function () {
            parentScope.showConfirmDialog("Arquivar", "Ao arquivar a sugestão ela não será analisada pelos administradores", (ok) => {
                if (ok) {
                    arquivar_sugestao($scope, $http).then((data) => {
                        parentScope.arquivar_sugestao($scope.id);
                        parentScope.showSimpleToast("Sugestão arquivada");
                        $mdDialog.hide();
                    });
                }
            });
        };

        $scope.desarquivar_sugestao = function () {
            parentScope.showConfirmDialog("Desarquivar", "Ao desarquivar a sugestão ela será analisada pelos administradores", (ok) => {
                if (ok) {
                    desarquivar_sugestao($scope, $http).then((data) => {
                        parentScope.desarquivar_sugestao($scope.id);
                        parentScope.showSimpleToast("Sugestão desarquivada");
                        $mdDialog.hide();
                    });
                }
            });
        };

        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
    }

    function showDialogSugestao(ev, scope, mdDialog, sugestao) {
        mdDialog.show({
            locals: {sugestao: sugestao, parentScope: scope},
            controller: SugestaoDialogController,
            templateUrl: 'template/detail_sugestao.tmpl.php',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: true
        }).then(function (answer) {

        }, function () {

        });
    }

    angular.module(__AppName__, ['ngMaterial', 'ngMessages'])
            .controller('FormCtrl', function ($scope, $window, $scope, $http, $mdDialog) {
                function updateList() {
                    get_sugestoes($scope, $http, (sugestoes) => {

                        $scope.sugestoes_pendentes.length = 0;
                        $scope.sugestoes_aceitas.length = 0;
                        $scope.sugestoes_recusadas.length = 0;
                        $scope.sugestoes_arquivadas.length = 0;

                        sugestoes.forEach((sugestao) => {
                            let data = {
                                registro: sugestao.registro,
                                datetime: convert_datetime_to_string(sugestao.data_criacao, sugestao.hora_criacao),
                                lastcommentary: "Sem comentários",
                                icon: "edit.svg",
                                id: sugestao.id,
                                status: sugestao.status
                            };
                            switch (sugestao.status) {
                                case "P":
                                    $scope.sugestoes_pendentes.push(data);
                                    break;
                                case "A":
                                    $scope.sugestoes_aceitas.push(data);
                                    break;
                                case "N":
                                    $scope.sugestoes_recusadas.push(data);
                                    break;
                                case "X":
                                    $scope.sugestoes_arquivadas.push(data);
                                    break;
                            }
                        });
                    });
                }

                window.topbarCtrl.onLogin = updateList;
                $scope.sugestoes_pendentes = [];
                $scope.sugestoes_aceitas = [];
                $scope.sugestoes_recusadas = [];
                $scope.sugestoes_arquivadas = [];

                $scope.isUserLogged = () => window.topbarCtrl.isUserLogged();

                $scope.arquivar_sugestao = (id) => {
                    var novo = $scope.sugestoes_pendentes.filter((sugestao) => sugestao.id !== id);
                    var find = $scope.sugestoes_pendentes.filter((sugestao) => sugestao.id === id)[0];
                    find.status = "X";
                    $scope.sugestoes_pendentes = novo;
                    $scope.sugestoes_arquivadas.push(find);
                };

                $scope.desarquivar_sugestao = (id) => {
                    var novo = $scope.sugestoes_arquivadas.filter((sugestao) => sugestao.id !== id);
                    var find = $scope.sugestoes_arquivadas.filter((sugestao) => sugestao.id === id)[0];
                    find.status = "P";
                    $scope.sugestoes_arquivadas = novo;
                    $scope.sugestoes_pendentes.push(find);
                };

                $scope.showSugestao = (event, sugestao) => {
                    showDialogSugestao(event, $scope, $mdDialog, sugestao);
                };

                window.dumpScope = () => {
                    return $scope;
                };

                function resize_event() {
                    $scope.mobile = ($window.innerWidth < 700);
                }

                resize_event();
                angular.element($window).bind('resize', resize_event);

                updateList();

            })
            .controller('OverviewCtrl', function ($scope, $mdDialog, $mdToast, $log) {
                append_dialogs_methods($scope, $mdDialog, $mdToast, $log);
            });

})();