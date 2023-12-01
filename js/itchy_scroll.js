$(document).ready(function () {
    $( window ).bind('scroll', function() {
        var navHeight =  1;
        var navWidth = 1;
        
         if ($(window).scrollTop() > navHeight ||
             $(window).scrollLeft() > navWidth) {
             $('#gameMenu').addClass('sticky');
         }
         else {
             $('#gameMenu').removeClass('sticky');
         }
     }); 
});