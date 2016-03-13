$(document).ready(function() {
    $('#olvidado').click(function(e) {
        e.preventDefault();
        $('div#form-olvidado').toggle('500');
    });
    $('#acceso').click(function(e) {
        e.preventDefault();
        $('div#form-olvidado').toggle('500');
    });
    $( "#login-recordar" ).submit(function( event ) {
        event.preventDefault();
        $.ajax({
            method: "POST",
            url: "/forgot",
            data: $("#login-recordar").serializeArray()
        })
        .done(function(res){
            $.bootstrapGrowl(res.message);
        });
    });
    $( "#login-form" ).submit(function( event ) {
        event.preventDefault();
        $.ajax({
            method: "POST",
            url: "/login",
            data: $("#login-form").serializeArray()
        })
        .done(function(res){
            if(res.redirect){
                window.location.href = res.redirect;
            }else{
                $.bootstrapGrowl(res.message);
            }
        });
    });
});
