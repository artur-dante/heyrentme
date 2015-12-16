$(function(){
    $(".placeholderStatusDashboard").each(function(){        
        $(this).attr("placeholder", defaultStatusText);
    });
    $(".btnSaveStatus").click(SaveStatusAndDiscount);        
    
    $(".selectpicker[id^='DiscountType_']").change(function(){
        var id = $(this).attr("id").split("_")[1];
        if(id == undefined || id == null){
            return;
        }                
        var selectedVal = $(this).val();
        if (selectedVal == 1){
            EnableDiscounts(4, "week", id);
        } else if (selectedVal == 2) {
            EnableDiscounts(24, "hour", id);
        } else {
            DisbleDiscounts(id);
        }

    });
    
    RestoreActiveDiscounts();
});

function RestoreActiveDiscounts(){    
    $(".selectpicker[id^='DiscountType_']").each(function(i,item){
        var id = $(item).attr("id").split("_")[1];
        var discountTypeVal = $("#HiddenDiscountType_"+id).val();
        if (discountTypeVal != undefined && discountTypeVal != -1) {
            SetValueAndDisable($(item), discountTypeVal)
            
            
        }
        
    });
}

function SetValueAndDisable($item, val){
    $item.val(val);
    $item.attr("disabled", "disabled");
    $item.selectpicker("refresh");
}

function DisbleDiscounts(id){
    var $control = $("#Duration_"+id);    
    $control.html("");
    $control.append(CreateOption(-1, "Dauer"));
    DisableControl($control);
    $control = $("#Percent_"+id);    
    $control.val("-1");
    DisableControl($control);
}


function EnableDiscounts(maxValue, type, id){
    var $control = $("#Duration_"+id);        
    $control.html("");
    $control.append(CreateOption(-1, "Dauer"));
    var suffix = "";    
    for(var i = 1; i <= maxValue; i++){        
        if (i > 1){
            suffix = "s";
        }
        $control.append(CreateOption(i, type + suffix));
    }
    EnableControl($control);    
    $control = $("#Percent_"+id);        
    EnableControl($control);
}

function DisableControl($control){
    $control.attr("disabled","disabled");    
    $control.selectpicker('refresh');
}

function EnableControl($control){
    $control.removeAttr("disabled");
    $control.selectpicker('refresh');
}

function CreateOption(val, text){
    if (val == -1){
        return $("<option>").val(val).text(text);
    } else {
        return $("<option>").val(val).text(val+" "+text);
    }
}


var defaultStatusText = "Neues zu diesem Angebot.. Z.B. Zur Zeit nicht verfügbar,Aktuell ein wenig günstiger etc.";
function SaveStatusAndDiscount(){
    
    var id = $(this).attr("id").split("_")[1];
    var text = $("#TxtStatus_"+id).val();
    var $msgBox = $("#DivMessage_"+id);
    $msgBox.removeClass();
    $msgBox.html("");
    $msgBox.hide();
    
    var errors = [];
    
    
    if (text == "" || text == defaultStatusText ){
        errors.push("Please insert status text.");                
    }
    
    if (text.length > 255) {
        errors.push("Status can have maximum 255 chars, "+ text.length + " given.");        
    }
    
    var discountTypeValue = $(".selectpicker[id='DiscountType_"+id+"']").val();
    var percentValue = $(".selectpicker[id='Percent_"+id+"']").val();
    var durationValue = $(".selectpicker[id='Duration_"+id+"']").val();
    
    if (discountTypeValue != -1) {                        
        if (percentValue == -1){
            errors.push("Please select discount percent.");
        }                
        if (durationValue == -1){
            errors.push("Please select discount duration.");
        }        
    }
   
    if (errors.length == 0) {
        var dataDict = {};
        if (discountTypeValue == -1 ) {
            dataDict = {
                "id": id,
                "text": text,
                "discountType": -1 
            } 
        } else {
            dataDict = {
                "id": id,
                "text": text,
                "discountType": discountTypeValue,
                "percent": percentValue,
                "duration": durationValue
            } 
        }

        $.ajax({
            url: url,
            type: 'post',
            data: dataDict,
            success: function(){
                WriteMessage($msgBox,[ "Offer saved correctly." ], false);
            },
            error: function(){
                WriteMessage($msgBox,[ "Some error occured." ], true);
            }        
        });
    } else {
        WriteMessage($msgBox, errors, true);
    }
    
}

function WriteMessage(container,messages, error){
    if (error){
        $(container).addClass("error");
    } else {
        $(container).addClass("correct");
    }
    for(var i=0; i < messages.length; i++){
        $(container).append($("<li>").text(messages[i]));
    }    
    $(container).show();
}