/**
 * 成功提示
 * @param text 内容
 * @param title 标题
 */
function op_success(text, title) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-center",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    toastr.success(text, title);
}
/**
 * 失败提示
 * @param text 内容
 * @param title 标题
 */
function op_error(text, title) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-center",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    toastr.error(text, title);
}

var ui = {
    success: function(message){
        op_success(message, '温馨提示');
    },

    error: function(message){
        op_error(message, '温馨提示');
    }
};

function handleajax(r) {
    if(typeof r == "string") {
        r = JSON.parse(r);
    }
    // show message
    if(r.message) {
        if(r.success) {
            ui.success(r.message);
        } else {
            ui.error(r.message);
        }
    }
    // redirect url
    if(r.url) {
        setTimeout(function() {
            if(r.url == "refresh") {
                location.reload();
                location.href = location.href;
            } else {
                location.href = r.url;
            }
        }, 1000);
    }
}

$(function(){
    $(document).on('click', '.ajaxhref', function(e) {
        e.preventDefault();

        // show confirm box if necessary
        var confirmmessage = $(this).attr('confirm');
        if(confirmmessage) {
            if(!confirm(confirmmessage)) {
                return false;
            }
        }
        // post ajax request
        var url = $(this).attr('href');
        $.get(url, {}, function(a,b,c){
            handleajax(a);
        });

        return false;
    })
})