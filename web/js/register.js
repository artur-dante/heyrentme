$(function(){
        var ajaxFormSubmit = function () {
            var $form = $(this);

            var options = {
                url: $form.attr("action"),
                type: $form.attr("method"),
                data: $form.serialize()
            };

            $.ajax(options).done(function (data) {    
                var $target = $("#formRegister");
                var $newHtml = $(data);                
                if (data.indexOf("User_Is_Registered") == -1){                                      
                    $target.replaceWith($newHtml);
                } else {                    
                    location.reload();
                }
            });

            return false;
        };
        
        $("#formRegister").submit(ajaxFormSubmit);
    });