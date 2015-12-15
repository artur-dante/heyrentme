$(function(){
    $(".placeholderStatusDashboard").each(function(){        
        $(this).attr("placeholder", defaultStatusText);
    });
    $(".btnSaveStatus").click(SaveStatus);
    
});
var defaultStatusText = "Neues zu diesem Angebot.. Z.B. Zur Zeit nicht verfügbar,Aktuell ein wenig günstiger etc.";
function SaveStatus(){
    
    var id = $(this).attr("id").split("_")[1];
    var text = $("#TxtStatus_"+id).val();
    var $msgBox = $("#DivMessage_"+id);
    $msgBox.removeClass();
    $msgBox.val("");
    $msgBox.hide();
    
    if (text == "" || text == defaultStatusText ){
        WriteMessage($msgBox,"Please insert status text.", true);        
        return;
    }
    
    if (text.length > 255) {
        WriteMessage($msgBox,"Status can have maximum 255 chars, "+ text.length + " given.", true);
        return;
    }
   
    $.ajax({
        url: url,
        type: 'post',
        data: { 
            "id": id,
            "text": text
        },
        success: function(){
            WriteMessage($msgBox,"Status saved correctly.", false);
        },
        error: function(){
            WriteMessage($msgBox,"Some error occured.", true);
        }        
    });
    
}

function WriteMessage(container,message, error){
    if (error){
        $(container).addClass("error");
    } else {
        $(container).addClass("correct");
    }
    $(container).text(message);
    $(container).show();
}