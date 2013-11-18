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
    $(document).on('click', '.ajaxhref', function() {
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