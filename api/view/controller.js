var oViewController = null;
var oPopupController = null;

PopupController = function(){
    var oThis= this; 
    oThis.return_fcn = null;
    
    oThis.showPopup = function(header,text,return_fcn = null, cancel_btn = false) {
        oThis.return_fcn = return_fcn;
        if(cancel_btn == false){
            $("#do_popup_cancel").hide();
        }
        else{
             $("#do_popup_cancel").show();
        }
        $("#popup_header").html(header)
        $("#popup_textarea").html(text)
        $("#blocker").fadeToggle(g_speed,null);
        $("#popup").fadeToggle(g_speed,null);
    }
    
    $("#do_popup_ok").off().click( function(event){
        if( oThis.return_fcn != null){
            oThis.return_fcn();
        }
        event.stopPropagation(); 
         $("#blocker").fadeToggle(g_speed,null);
         $("#popup").fadeToggle(g_speed,null);
        
    });
     $("#do_popup_cancel").off().click( function(event){
        event.stopPropagation(); 
        $("#blocker").fadeToggle(g_speed,null);
        $("#popup").fadeToggle(g_speed,null);
     });
                                       
    $("#popup").click( function(event){
        event.stopPropagation(); 
    });
}
ViewController = function () {
    var oThis = this;
    
    oThis._testEmail = function(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    oThis._testPW = function(pw) {
        return pw.length > 5;
    }
    oThis._testName = function(name) {
        return name.length > 3;
    }
    oThis._testToken = function(val) {
        return val.length == 5 && Number.isInteger(parseInt(val, 10));
    }
    oThis._encrypt= function(str,salt) {
        var toEnctypt = str;
        var encoded = "";
        for (i=0; i<str.length;i++) {
            var a = str.charCodeAt(i);
            var b = a ^ salt;    // bitwise XOR with any number, e.g. 123
            encoded = encoded+String.fromCharCode(b);
        }
        return encoded;
    }
    oThis._validateLoginInput = function() {
        
        var login_email = $("#login_email").val();
        var login_password = $("#login_password").val();
        if(!oThis._testEmail(login_email)){
            $("#login_email").addClass("input_error");
            $(".login_feedback").html("Ogiltig epost!");
            return false;
        }
        else{
            $("#login_email").removeClass("input_error");
            $(".login_feedback").html("");
            
        }
        if(!oThis._testPW(login_password)){
            $("#login_password").addClass("input_error");
            $(".login_feedback").html("Lösenordet måste vara längre än 5 tecken.");
            return false;
        }
        else{
            $("#login_password").removeClass("input_error");
            $(".login_feedback").html("");
        }
        return true;
    }
    oThis._validateRegistrationInput = function() {
        
        var email = $("#register_email").val();
        var password = $("#register_password").val();
        var name = $("#register_name").val();
        if(!oThis._testEmail(email)){
            $("#register_email").addClass("input_error");
            $(".register_feedback").html("Ogiltig epost!");
            return false;
        }
        else{
            $("#register_email").removeClass("input_error");
            $(".register_feedback").html("");
            
        }
        if(!oThis._testPW(password)){
            $("#register_password").addClass("input_error");
            $(".register_feedback").html("Lösenordet måste vara längre än 5 tecken.");
            return false;
        }
        else{
            $("#register_password").removeClass("input_error");
            $(".register_feedback").html("");
        }
        if(!oThis._testName(name)){
            $("#register_name").addClass("input_error");
            $(".register_feedback").html("Namnet är ogiltigt...");
            return false;
        }
        else{
            $("#register_name").removeClass("input_error");
            $(".register_feedback").html("");
        }
        return true;
    }
    oThis.menuVisible = false;
    oThis.toggleShowSlideMenu = function(){ 
        var width = 400;
        var left = $( window ).width() - 400
        if(oThis.menuVisible){
            left = $( window ).width();
            width = 0
        }
        else{
             oThis._poplateSlideMenu();
        }

        oThis.menuVisible = !oThis.menuVisible;
        $( ".slideMenu" ).animate({left: left + 'px', width: width + 'px'},oThis._anmiateSlideMenuComplete); 
    }
    oThis.hideSlideMenu = function(){
        oThis.menuVisible = true;
        oThis.toggleShowSlideMenu();
    }
    oThis._anmiateSlideMenuComplete = function(){
        if(!oThis.menuVisible){
             $( ".slideMenu" ).html("");
        }
    }
    oThis._onWnResizeUpdadeSlideMenu = function(){
        var width = $( window ).width();
        if(oThis.menuVisible){
            width = $( window ).width() - $( ".slideMenu" ).width()
        }
        $( ".slideMenu" ).css('left', width );
    } 
    oThis.arrViewItems = [];
    oThis.defaultInsideFcn = null;
    oThis.registerViewItem = function(menuName, callFunction, isDefault){
        if(isDefault){
            oThis.defaultInsideFcn = callFunction;
            oThis.defaultInsideFcn();
        }
        var viewItem = {name: menuName, fcn: callFunction};
         oThis.arrViewItems.push(viewItem);
    } 
    oThis._buildViewItem = function(viewItem){
        var divValues = $( document.createElement('div') );
        divValues.addClass( "btn" );
        divValues.attr("id",viewItem.name);
        divValues.html(viewItem.name);
        divValues.off().click(function(){
            oThis._viewItemClicked(this.id);
            oThis.hideSlideMenu();
        });
        $( ".slideMenu" ).append(divValues);
    }
    oThis._viewItemClicked = function(id){
        for (i = 0; i < oThis.arrViewItems.length; i++) {
            viewItem = oThis.arrViewItems[i];
            if(viewItem.name == id){
                viewItem.fcn();
                return;
            }
        }
    }
    oThis._poplateSlideMenu = function(){
        jsonObj = {
            g_session: g_session,
            request: 'get_slide_menu',
        }
        $.post(g_api_server, jsonObj , function(result){
            var myObj = JSON.parse(result);
            g_loginstate = myObj.loginState;
            
            var divValues = $( document.createElement('div') );
            divValues.html("Användare: " + myObj.name);
            $( ".slideMenu" ).append(divValues);
            
            
            //****************
            
            oThis.arrViewItems.forEach(oThis._buildViewItem);

            /*
            var divValues = $( document.createElement('div') );
            divValues.addClass( "btn" );
            divValues.html("Settings");
            divValues.off().click(function(){
                oThis.oPopupController.showPopup("Show settings","Show my settings");
                
            });
            $( ".slideMenu" ).append(divValues);
            */
            
            var divValues = $( document.createElement('div') );
            divValues.addClass( "btn" );
            divValues.html("Logga ut");
            divValues.off().click(function(){
                
                jsonObj = {
                    g_session: g_session,
                    request: 'do_logout'
                }
                $.post(g_api_server, jsonObj , function(result){
                    $(".inside").hide();
                    $(".user").hide();
                    $(".register").hide();
                    $(".resetpw").hide();
                    $(".authenticate").hide();
                    $(".register").hide();
                    $(".login").show();

                    var myObj = JSON.parse(result);
                    g_session = myObj.g_session;
                    g_loginstate = myObj.loginState;

                });
                
            });
            $( ".slideMenu" ).append(divValues);
            
        });   
    }
    
    oThis.synchViews = function () {
        $(".inside").hide();
        $(".wallet").hide();
        $(".user").hide();
        $(".register").hide();
        $(".resetpw").hide();
        $(".register").hide();
        $(".authenticate").hide();
        
        if(oThis.defaultInsideFcn != null){
            oThis.defaultInsideFcn();
        }
        
        if(g_loginstate == "UM_LOGINSTATE_UN_AUTHENTICATED_NEW" ||
           g_loginstate == "UM_LOGINSTATE_AUTHENTICATED"){  
            $(".login").hide();
            $(".inside").show();
        }
        else if(
            g_loginstate == "UM_LOGINSTATE_UN_AUTHENTICATED" ||
            g_loginstate == "UM_LOGINSTATE_UN_AUTHENTICATED_RETURNING" ||
            g_loginstate == "UM_LOGINSTATE_UN_AUTHENTICATED_NEW"){
            $(".authenticate").show();
            $(".inside").hide();
        }
        
        $("#do_show_register").click( function () {
             $(".register").show();
        });
        $("#do_cancel_register").click( function () {
             $(".register").hide();
        });
        $("#do_cancel_authenticate").click( function () {
             $(".authenticate").hide();
        });     
        $("#do_logout").click( function () {
            jsonObj = {
                g_session: g_session,
                request: 'do_logout'
            }
            $.post(g_api_server, jsonObj , function(result){
                $("#authenticate_token").val("");
                $(".user").hide();
                $(".register").hide();
                $(".resetpw").hide();
                $(".authenticate").hide();
                $(".register").hide();
                $(".inside").hide();
                $(".login").show();
                
                var myObj = JSON.parse(result);
                g_session = myObj.g_session;
                g_loginstate = myObj.loginState;
                window.location.reload(true);
                
            });
        });
        
        $("#do_register").one("click", function () {

            if(oThis._validateRegistrationInput()){
               var email = $("#register_email").val();
               var password = $("#register_password").val();
               var name = $("#register_name").val();
               
               jsonObj = {
                    g_session: g_session,
                    request: 'get_salt'
                }
                $.post(g_api_server, jsonObj , function(result){
                    var myObj = JSON.parse(result);
                    g_loginstate = myObj.loginState;
                    
                    salt = myObj.salt;
                    md5_password = md5(password);
                    password = g_session + "." + md5_password;
                    password = oThis._encrypt(password,salt);
                    jsonObj = {
                        g_session: g_session,
                        request: 'do_register',
                        email: email,
                        name: name,
                        password:password,
                    }
                    
                    $.post(g_api_server, jsonObj , function(result){
                       
                        var myObj = JSON.parse(result);
                        g_loginstate = myObj.loginState;
                        
                        if(myObj.result == "success") {
                            $(".authenticate_feedback").html("Ett mail med inloggningskod har skickats till dig.");
                            $(".authenticate").show();
                            $(".register").hide();
                        }
                        else{
                            if( myObj.last_error ==1 ){
                                $(".register_feedback").html("Epostadressen finns redan...");
                            }
                            else{
                                (".register_feedback").html("Okänt fel...");
                            }
                                
                        }
                    });
                });
           }
        });
        $("#do_authenticate").click( function () {
            token = $("#authenticate_token").val();
            
            if(oThis._testToken(token)){
                $(".authenticate_feedback").html("");
                jsonObj = {
                    g_session: g_session,
                    request: 'do_authenticate',
                    token: token
                }
                $.post(g_api_server, jsonObj , function(result){
                    var myObj = JSON.parse(result);
                    g_loginstate = myObj.loginState;
                    
                    if(myObj.loginState == "UM_LOGINSTATE_AUTHENTICATED"){
                        $(".wallet").hide();
                        $(".user").hide();
                        $(".register").hide();
                        $(".resetpw").hide();
                        $(".authenticate").hide();
                        $(".register").hide();
                        $(".login").hide();
                        $(".inside").show();
                    }
                    else if(myObj.loginState == "UM_LOGINSTATE_UN_AUTHENTICATED_RETURNING"){
                        $(".authenticate_feedback").html("Gammal eller inkorrekt kod.");
                    }
                });
            }
            else{
                $(".authenticate_feedback").html("Gammal eller inkorrekt kod.");
            }    
        });        
        $("#do_login").click( function () {
            if(oThis._validateLoginInput()){
                var login_email = $("#login_email").val();
                var login_password = $("#login_password").val();
                
                md5_password = md5(login_password);
                sess_encoded = md5(g_session+md5_password);
                

                jsonObj = {
                    g_session: g_session,
                    request: 'do_start_login',
                    email: login_email,
                    sess_encoded: sess_encoded,
                }
                $.post(g_api_server, jsonObj , function(result){
                    
                    var myObj = JSON.parse(result);
                    g_loginstate = myObj.loginState;
                    if(myObj.loginState == "UM_LOGINSTATE_UN_AUTHENTICATED_NEW" ||
                       myObj.loginState == "UM_LOGINSTATE_UN_AUTHENTICATED_RETURNING"){
                        $(".authenticate_feedback").html("Ett mail med inloggningskod har skickats till dig.");
                        
                        $(".wallet").hide();
                        $(".user").hide();
                        $(".register").hide();
                        $(".resetpw").hide();
                        $(".register").hide();
                        $(".login").hide();
                        $(".authenticate").show();
                    }
                    else{
                         $(".login_feedback").html("Inloggning misslyckades. Felaktig mail eller kod. <br /><br /> Om du har problem att logga in, skicka ett mail till vulpinusminima@gmail.com");
                    }
                });
            }
        });
        $("#do_resetpw").click( function () {
           $(".resetpw").show();
        });
        $("#do_request_reset").click( function() {
            var reset_email = $("#reset_email").val();
            if(oThis._testEmail(reset_email)){
                 jsonObj = {
                    g_session: g_session,
                    request: 'do_request_reset',
                    email: reset_email,
                }
                $.post(g_api_server, jsonObj , function(result){
                    var myObj = JSON.parse(result);
                    g_loginstate = myObj.loginState;
                    
                    $(".reset_feedback").html("Ett mail med instruktioner har skickats till dig.<br/>(Om adressen kunde återfinnas)...");
                    
                    setTimeout(function() { 
                        $(".resetpw").hide();
                    }, 5000);
                    
                });
            }
            else{
                $(".reset_feedback").html("Ogiltig epost!");
            }
            
        });
        $("#do_cancel_reset").click( function() {
            $(".resetpw").hide();
        });  
        $(".show_menu").off().click( function(event){
            event.stopPropagation(); 
            oThis.toggleShowSlideMenu();
        });
        $("body").click( function(){
             oThis.hideSlideMenu();
        });
        $('.slideMenu').click( function(event){
            event.stopPropagation(); 
        });
        $( window ).resize(function() {
            oThis._onWnResizeUpdadeSlideMenu();
        });
        oThis._onWnResizeUpdadeSlideMenu();
    }
}

$(window).ready(function () {
    oViewController = new ViewController();
    oPopupController = new PopupController();
    oViewController.synchViews();
    oViewController.oPopupController = oPopupController;
    if(oInsideController){
        oInsideController.oPopupController = oPopupController;
    }
});

$(document).ready(function () {
    $( window ).bind('scroll', function() {
        var navHeight =  10;
        var navWidth = 10;
        
         if ($(window).scrollTop() > navHeight ||
             $(window).scrollLeft() > navWidth) {
             $('#insideMenu').addClass('sticky');
         }
         else {
             $('#insideMenu').removeClass('sticky');
         }
    });    
});