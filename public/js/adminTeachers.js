$(document).ready(function() {
$("#savedata").click(function() {
    var con= confirm("Do you want to save data?");
    if (save == true) {
        var name= $("#teachername").val();
        var email= $("#teacheremail").val();
        var faculty= $("#faculty").val();
        var subjects= $("#subjects").val();
            if(name != "" && email !="" && faculty != "" && subjects != ""){
                return true;
            }
            else {
                 if(name == ""){
                    $("#teachername").css("border":"1px solid #cc0000");
                    $("#teachername").html("placeholder": "please fill this field...");
            }
                 if(email == ""){
                    $("#teachername").css("border":"1px solid #cc0000");
                    $("#teachername").html("placeholder": "please fill this field...");
                 }
                 if(faculty == ""){
                    $("#teachername").css("border":"1px solid #cc0000");
                    $("#teachername").html("placeholder": "please fill this field...");
            }
                 if(subjects == ""){
                    $("#teachername").css("border":"1px solid #cc0000");
                    $("#teachername").html("placeholder": "please fill this field...");
            }
                return false;
            }
    }
    else return false;
    });
});

