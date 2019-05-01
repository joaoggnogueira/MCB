'use strict';
(function () {

    window.cRequest = new function () {
        
        this.postJson = function(url,json,callback){
            return $.ajax({
                url: "./request/"+url,
                type: 'POST',
                dataType: 'json',
                data: json,
                timeout: 20000,
                success: function (data) {
                    if (data.success) {
                        callback(data);
                    } else {
                        swal({title:"Opss ...",type: 'warning',text:data.message});
                        console.log(data);
                    }
                },
                error: function (data) {
                    swal({title:"Error", type: 'error',html:data.responseText});
                    console.log(data);
                }
            });
        };
        
    };

})();
