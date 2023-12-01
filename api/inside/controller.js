function InsideViewController()
{
    var oThis = this;
    oThis.oPopupController = null;
    oThis.curr_view = "none";
    oThis.eventId = null;
    oThis.answer = -1;
    //Setup and redraw...
    
    oThis.initialize = function(){
            oViewController.registerViewItem("Start", oThis._menuItemHome, true);
            oViewController.registerViewItem("Principer", oThis._menuItemPrinciples,false);
    }
    
    oThis.setupView = function(){    
    }
    
    oThis.home_data = null;
    oThis.principles_data = null;
    oThis.timeout = null;
    oThis._isclicked = false;
    
    oThis.attendClick = function(elem){
        console.log(elem);
        if(oThis._isclicked == true){
            return
        }
        oThis._isclicked = true
        
        val = $(elem).val();
        answer = 0;
        if(val == "yes"){
            answer = 1;
        }
        jsonObj = {
            g_session: g_session,
            request: 'set_answer',
            eventId: oThis.eventId,
            answer: answer
        }
        $.post(g_inside_server, jsonObj , function(result){
            console.log("post");
            var myObj = JSON.parse(result);
            g_loginstate = myObj.loginState;
            
            var header = "Tack för ditt svar!";
            var text = "Ditt svar har registrerats.<br /><br />";
            var participate = "";
            var end = "Välkommen! Vi hoppas att kommer att få galet roligt!<br /><br />";
            if(myObj.answer == 0){
                participate = "inte ";
                var end = "Tråkigt att du inte kan komma! <br />Kanske nästa gång?<br /><br />";
            }
            
            text += "Du har svarat att du " + participate + "kommer att delta " + myObj.beautifulDate +".<br /><br />";
            text += end;
            oThis._isclicked = false
            oThis.oPopupController.showPopup(header,text,null, false);
            
        });
    }
    
    oThis._menuItemHome = function(){
        if( g_loginstate == "UM_LOGINSTATE_UNREGISTERED" ){
             $("#content_inside").html("");
        }
        if( oThis.home_data == null){
            oThis._requestHomeData();
        }
        else{
            oThis._diplayHomeDataStart();
        }
    }
    
    oThis._diplayHomeDataStart = function(){
        oThis.curr_view = "home";
        oThis._diplayHomeDataItem(0,0);
    }
    oThis._diplayHomeDataItem = function(view, item){
        if( oThis.curr_view == "home"){
            item_data = oThis.home_data[view][item];
            wait_timer = item_data.wait;
            if(item == 0){ // this is a new view. we need to fade out and clear view

                $("#content_inside").fadeOut(250, function() {
                    $(this).html("");
                    $(this).show();
                    $(item_data.html)
                        .hide()
                        .appendTo("#content_inside")
                        .fadeIn(item_data.speed,  function() {
                        oThis._prepareNextHomeDataItem(view, item, wait_timer);
                    });   
                });   
            }
            else{
                $(item_data.html)
                        .hide()
                        .appendTo("#content_inside")
                        .fadeIn(item_data.speed,  function() {
                        oThis._prepareNextHomeDataItem(view, item, wait_timer);
                    });   
            } 
            $('input[name ="attend"]').click(function(event){
                event.stopPropagation();
                oInsideController.attendClick(this);
            });
        }
    }
    oThis._prepareNextHomeDataItem = function(view, item, wait ){
        if( oThis.curr_view == "home"){
            num_views = oThis.home_data.length;
            num_items = oThis.home_data[view].length;

            if(item == num_items - 1){
                if(view == num_views - 1){
                    view = 0;
                    item = 0;
                }
                else{
                    view += 1;
                    item = 0;
                }
            }
            else{
                 item += 1;
            }
            
            clearTimeout( oThis.timeout);
            oThis.timeout = setTimeout(function() { 
                oThis._diplayHomeDataItem(view, item);
            }, wait);
        }
    }
    oThis._requestHomeData = function(){
        jsonObj = {
            g_session: g_session,
            request: 'get_home_data',
            id: oThis.homeItemId
        }
        $.post(g_inside_server, jsonObj , function(result){
            var myObj = JSON.parse(result);
            g_loginstate = myObj.loginState;
            
            oThis.eventId = myObj.eventId;
            if(myObj.hasAnswered == 1){
                oThis.answer = myObj.answer;
            }
            
            
            oThis.home_data = myObj.content;
            oThis._diplayHomeDataStart();
        });
    }
    
    oThis._menuItemPrinciples = function(){
        oThis.curr_view == "principles"
        clearTimeout( oThis.timeout);
        
        if( g_loginstate == "UM_LOGINSTATE_UNREGISTERED" ){
             $("#content_inside").html("");
        }
        if( oThis.principles_data == null){
            oThis._requestPrinciplesData();
        }
        else{
            oThis._diplayPrinciplesDataStart();
        }
    }
    oThis._diplayPrinciplesDataStart = function(){
        oThis.curr_view = "principles";
        oThis._diplayPrinciplesDataItem(0,0);
    }
    oThis._diplayPrinciplesDataItem = function(view, item){
        if( oThis.curr_view == "principles"){
            item_data = oThis.principles_data[view][item];
            wait_timer = item_data.wait;
            if(item == 0){ // this is a new view. we need to fade out and clear view

                $("#content_inside").fadeOut(250, function() {
                    $(this).html("");
                    $(this).show();
                    $(item_data.html)
                        .hide()
                        .appendTo("#content_inside")
                        .fadeIn(item_data.speed,  function() {
                        oThis._prepareNextPrincipleDataItem(view, item, wait_timer);
                    });   
                });   
            }
            else{
                $(item_data.html)
                        .hide()
                        .appendTo("#content_inside")
                        .fadeIn(item_data.speed,  function() {
                        oThis._prepareNextPrincipleDataItem(view, item, wait_timer);
                    });   
            }   
        }
    }
    oThis._prepareNextPrincipleDataItem = function(view, item, wait ){
        if( oThis.curr_view == "principles"){
            num_views = oThis.home_data.length;
            num_items = oThis.home_data[view].length;

            if(item == num_items - 1){
                if(view == num_views - 1){
                    view = 0;
                    item = 0;
                }
                else{
                    view += 1;
                    item = 0;
                }
            }
            else{
                 item += 1;
            }
            
            clearTimeout( oThis.timeout);
            oThis.timeout = setTimeout(function() { 
                oThis._diplayPrinciplesDataItem(view, item);
            }, wait);
        }
    }
     oThis._requestPrinciplesData = function(){
        jsonObj = {
            g_session: g_session,
            request: 'get_principles_data',
            id: oThis.homeItemId
        }
        $.post(g_inside_server, jsonObj , function(result){
            var myObj = JSON.parse(result);
            g_loginstate = myObj.loginState;
            oThis.principles_data = myObj.content;
            oThis._diplayPrinciplesDataStart();
        });
    }
}
var oInsideController = null;
$(window).ready(function () {
    oInsideController = new InsideViewController();
    if(oPopupController){
        oInsideController.oPopupController = oPopupController;
    }
    oInsideController.initialize();
});