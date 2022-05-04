function bind_text(id, text) {
    let dom = document.getElementById(id);
    if (dom) {
        document.getElementById(id).innerHTML = text;
    } else {
        console.error(`Elemento ${id} não encontrado`);
    }
}

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function convert_datetime_to_string(date, time) {
    var d = new Date(date + "T" + time + "Z");

    var minutesOffset = d.getTimezoneOffset();
    var fix_d = new Date();

    fix_d.setDate(d.getDate());
    fix_d.setMonth(d.getMonth());
    fix_d.setFullYear(d.getFullYear());
    fix_d.setHours(d.getHours());
    fix_d.setMinutes(d.getMinutes() + minutesOffset);
    fix_d.setSeconds(d.getSeconds());

    return pad(fix_d.getDay(), 2) + "/" + pad(fix_d.getMonth() + 1, 2) + "/" + pad(fix_d.getFullYear(), 4) + " ás " + pad(fix_d.getHours(), 2) + ":" + pad(fix_d.getMinutes(), 2);
}

function isNull(v) {
    return (v === false || v === undefined || v === null || v === "");
}

function append_dialogs_methods($scope, $mdDialog, $mdToast, $log) {

    $scope.showMessageDialog = (title, text, doToast, callback_success) => {
        var confirm = $mdDialog.alert()
                .title(title)
                .clickOutsideToClose(true)
                .textContent(text)
                .ariaLabel('Confirmar mensagem')
                .ok('OK');

        $mdDialog.show(confirm).then(function () {
            if (doToast) {
                $scope.showSimpleToast(text);
            }
            if (callback_success) {
                callback_success();
            }
        });
    };

    $scope.showSimpleToast = (text, position = "bottom right") => {
        $mdToast.show(
                $mdToast.simple()
                .textContent(text)
                .position(position)
                .hideDelay(3000))
                .then(function () {
                    $log.log('Toast dismissed.');
                }).catch(function () {
            $log.log('Toast failed or was forced to close early by another toast.');
        });
    };

    $scope.showConfirmDialog = (title, text, callback) => {
        var confirm = $mdDialog.confirm()
                .title(title)
                .textContent(text)
                .ariaLabel('Confirmar ação')
                .ok('Confirmar!')
                .cancel('Cancelar');

        $mdDialog.show(confirm).then(function () {
            callback(true);
        }, function () {
            callback(false);
        });
    };

}

function get_url_param(name) {
    var url_string = window.location.href;
    var url = new URL(url_string);
    return url.searchParams.get(name);
}