
$(function(){
        var ajaxFormSubmit = function () {
            var $form = $(this);

            var options = {
                url: $form.attr("action"),
                type: $form.attr("method"),
                data: $form.serialize()
            };

            $.ajax(options).done(function (data) {
                var $target = $("#formLogin");
                var $newHtml = $(data);                
                                
                if (data.indexOf("User_Is_Logged") == -1){                                      
                    $target.replaceWith($newHtml);
                } else {              
                    //location.reload();
                    location.href = "provider/profil";
                }
                
            });

            return false;
        };
        
        $("#formLogin").submit(ajaxFormSubmit);
    });