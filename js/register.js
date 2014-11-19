$(function() {
    $('#city').keyup(function(k){
        switch(k){
            case 13:
            case 27:
            case 38:
            case 40:
                break;

            default :
                $.post( "/user/cities", {'letter' : $('#city').val()}, function( data ) {
                    if(data){
                        var html = '';
                        for(var i=0; i<data.length; i++){
                            html += '<div class="advice_variant">'+ data[i] + '</div>';
                        }
                        $("#city_advice").html(html).show();

                        $('.advice_variant').click(function(){
                            var text = ($(this).text());
                            $('#city').val(text);
                            $("#city_advice").hide();
                        });

                    }
                }, 'json');
        }

    });


    $( "#register_form" ).submit(function( event ) {
        var login = $('#login').val();
        var password = $('#password').val();
        var city = $('#city').val();
        if(login && password && city){
            return true;
        } else {
            alert('Не заполнены поля');
            return false;
        }
    });
});
