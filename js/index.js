$( document ).ready(function() {
    $("#adminLogIn").click(function() {
        $("#registerDiv").hide(); 
        $("#loginDiv").fadeToggle("slow");
    });
    
    $("#resetBut").click(function() {
        $("#registerDiv").fadeOut("slow");
        $("#registerFade").delay(1000).fadeIn("slow");
    });
    
    $("#regButton").click(function() {
        $("#registerFade").fadeOut("slow");
        $("#registerDiv").delay(1000).fadeIn("slow");
    });
});