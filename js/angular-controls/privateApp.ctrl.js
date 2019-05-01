/* Do sugestao.php */
/* global __AppName__, bind_text */

(function () {

    angular
            .module(__AppName__, ['ngMaterial', 'ngMessages'])
            .controller('FormCtrl', function ($scope, $window, $scope, $http, $mdDialog) {
                function updateList() {
                    
                }

                window.topbarCtrl.onLogin = updateList;
                $scope.isUserLogged = () => window.topbarCtrl.isUserLogged();
                
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