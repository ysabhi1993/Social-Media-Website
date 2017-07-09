$(document).ready(function(){
   
    //On click sign-up, hide login and show registration
    $("#signup").click(function(){
         $("#first").slideUp("slow",function(){
             $("#second").slideDown("slow");
         });
    });
    
    //On click sign-up, show login and hide registration
    $("#signin").click(function(){
         $("#second").slideUp("slow",function(){
             $("#first").slideDown("slow");
         });
    });
    
});