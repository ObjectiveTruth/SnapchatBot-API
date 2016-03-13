$("input[type=password]").keyup(function(){
    var ucase = new RegExp("[A-Z]+");
    var lcase = new RegExp("[a-z]+");
    var num = new RegExp("[0-9]+");
    var isValid = true;

    if($("#password1").val().length >= 8){
        $("#8char").removeClass("glyphicon-remove");
        $("#8char").addClass("glyphicon-ok");
        $("#8char").css("color","#00A41E");
    }else{
        isValid = false;
        $("#8char").removeClass("glyphicon-ok");
        $("#8char").addClass("glyphicon-remove");
        $("#8char").css("color","#FF0004");
    }

    if(ucase.test($("#password1").val())){
        $("#ucase").removeClass("glyphicon-remove");
        $("#ucase").addClass("glyphicon-ok");
        $("#ucase").css("color","#00A41E");
    }else{
        isValid = false;
        $("#ucase").removeClass("glyphicon-ok");
        $("#ucase").addClass("glyphicon-remove");
        $("#ucase").css("color","#FF0004");
    }

    if(lcase.test($("#password1").val())){
        $("#lcase").removeClass("glyphicon-remove");
        $("#lcase").addClass("glyphicon-ok");
        $("#lcase").css("color","#00A41E");
    }else{
        isValid = false;
        $("#lcase").removeClass("glyphicon-ok");
        $("#lcase").addClass("glyphicon-remove");
        $("#lcase").css("color","#FF0004");
    }

    if(num.test($("#password1").val())){
        $("#num").removeClass("glyphicon-remove");
        $("#num").addClass("glyphicon-ok");
        $("#num").css("color","#00A41E");
    }else{
        isValid = false;
        $("#num").removeClass("glyphicon-ok");
        $("#num").addClass("glyphicon-remove");
        $("#num").css("color","#FF0004");
    }

    if($("#password1").val() == $("#password2").val()){
        $("#pwmatch").removeClass("glyphicon-remove");
        $("#pwmatch").addClass("glyphicon-ok");
        $("#pwmatch").css("color","#00A41E");

    }else{
        isValid = false;
        $("#pwmatch").removeClass("glyphicon-ok");
        $("#pwmatch").addClass("glyphicon-remove");
        $("#pwmatch").css("color","#FF0004");
    }
    if(isValid){
        $("#changepass").removeAttr("disabled").button('refresh');
    }else{
        $("#changepass").attr("disabled", "disabled").button('refresh');
    }
});

$(document).ready(function() {
    $( "#passwordForm" ).submit(function( event ) {
        event.preventDefault();
        var token = window.location.href.split('token=').pop();
        $.ajax({
            method: "POST",
            url: "/reset/" + token,
            data: $("#passwordForm").serializeArray()
        })
        .done(function(res){
            if(res.redirect){
                window.location.href = res.redirect;
            }else{
                if(res.message){
                    $.bootstrapGrowl(res.message);
                }else{
                    $.bootstrapGrowl("Something went wrong, probably no valid token");
                }
            }
        });
    });
});
