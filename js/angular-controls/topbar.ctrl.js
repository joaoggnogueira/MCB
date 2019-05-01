/* global gapi, __AppName__ */

(function () {
    angular.module(__AppName__).controller('TopbarCtrl', function ($scope, $http) {

        $scope.profile = false;
        $scope.signedin = false;
        $scope.profile_compiled = {
            name: "",
            image: ""
        };

        $scope.logoff = () => {

            $http({
                method: 'POST',
                url: './request/destroy_session.php',
                data: JSON.stringify({})
            }).then((response) => {
                var data = response.data;
                if (data.success) {
                    $scope.signedin = false;
                    $scope.$parent.showSimpleToast("✔ Sessão encerrada com sucesso");
                } else {
                    $scope.$parent.showSimpleToast("✖ Falha ao encerrar sessão");
                }
            }).catch((response) => {
                $scope.$parent.showMessageDialog("✖ Opss, Erro interno 0x1");
                console.error(response);
            });
        };

        window.onSignIn = (googleUser) => {
            if ($scope.signedin === false) {
                let basic_profile = googleUser.getBasicProfile();

                let profile = {
                    id: basic_profile.getId(),
                    fullname: basic_profile.getName(),
                    givenname: basic_profile.getGivenName(),
                    familyname: basic_profile.getFamilyName(),
                    imageurl: basic_profile.getImageUrl(),
                    email: basic_profile.getEmail()
                };

                push_session(profile);
                dump_profile();

                var auth2 = gapi.auth2.getAuthInstance();
                auth2.signOut().then(function () {
                    console.log('User signed out.');
                });
            }
        };

        window.topbarCtrl = {
            isUserLogged: () => $scope.signedin,
            getUser: () => $scope.profile,
            onLogin: null
        };

        function compile_profile() {
            $scope.signedin = true;
            $scope.profile_compiled = {
                name: $scope.profile.fullname,
                image: $scope.profile.imageurl,
                credentials: ($scope.profile.isAdmin ? "Administrador" : "Visitante")
            };
        }

        function init_profile() {
            get_session((data) => {
                $scope.profile = data.data;
                $scope.profile.isAdmin = data.admin;
                compile_profile();
                dump_profile();
            });
        }

        function push_session(profile) {
            $http({
                method: 'POST',
                url: './request/push_session.php',
                data: JSON.stringify({google_profile: profile})
            }).then((response) => {
                var data = response.data;
                if (data.success) {
                    $scope.profile = profile;
                    $scope.profile.isAdmin = data.admin;
                    compile_profile();
                    if (window.topbarCtrl.onLogin) {
                        window.topbarCtrl.onLogin();
                    }
                    $scope.$parent.showSimpleToast("✔ Acesso realizado com sucesso");
                } else {
                    $scope.$parent.showSimpleToast("✖ Falha ao realizar acesso");
                }
            }).catch((response) => {
                $scope.$parent.showMessageDialog("✖ Opss, Erro interno 0x1");
                console.error(response);
            });
        }

        function get_session(callback) {
            $http({
                method: 'POST',
                url: './request/get_session.php',
                data: JSON.stringify({})
            }).then((response) => {
                var data = response.data;
                if (data.success) {
                    callback(data.data);
                } else {
                    $scope.$parent.showSimpleToast("Faça o acesso na conta Google para ter acesso as funcionalidades", "top right");
                }
            }).catch((response) => {
                $scope.$parent.showMessageDialog("✖ Opss, Erro interno 0x2");
                console.error(response);
            });
        }

        var dump_profile_flag = false;

        window.active_dump_profile = () => dump_profile_flag = true;

        function dump_profile() {
            if (dump_profile_flag) {
                console.log('ID: ' + $scope.profile.id);
                console.log('Full Name: ' + $scope.profile.fullname);
                console.log('Given Name: ' + $scope.profile.givenname);
                console.log('Family Name: ' + $scope.profile.familyname);
                console.log('Image URL: ' + $scope.profile.imageurl);
                console.log('Email: ' + $scope.profile.email);
            }
        }

        init_profile();
    });

})();
