$(document).ready(function(){
    $('#submit_new_password').click(function(){
        $.post('/user/update', {'param' : 'password', 'value' : $('#user_new_password').val()}, function(data){
            if(data){
                alert('Пароль обновлен');
                $('#user_new_password').val('');
            }
        });
    });


    $('#submit_new_city').click(function(){
        $.post('/user/update', {'param' : 'city', 'value' : $('#user_new_city').val()}, function(data){
            if(data){
                alert('Город обновлен');
            }
        });
    });


    $('#user_new_city').keyup(function(k){
        switch(k){
            case 13:
            case 27:
            case 38:
            case 40:
                break;

            default :
                $.post( "/user/cities", {'letter' : $('#user_new_city').val()}, function( data ) {
                    if(data){
                        var html = '';
                        for(var i=0; i<data.length; i++){
                            html += '<div class="advice_variant">'+ data[i] + '</div>';
                        }
                        $("#city_advice").html(html).show();

                        $('.advice_variant').click(function(){
                            var text = ($(this).text());
                            $('#user_new_city').val(text);
                            $("#city_advice").hide();
                        });

                    }
                }, 'json');
        }

    });
})
