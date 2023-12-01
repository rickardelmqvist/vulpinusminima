<?php
    define("PAGE_TITLE","Vulpinus Minima");
    define("PAGE_STYLESHEET","basestyle.css");

    define("PAGE_MAIN_JSWINDOWSCROLL","js/itchy_scroll.js");
    //define("PAGE_MAIN_JSAPP","js/itchy_app.js");

    define("PAGE_MD5_ENCRYPT","js/md5.min.js");
    define("PAGE_FILESAVER","js/filsaver.min.js");

    define("PAGE_MAIN_JSDEFINES","defines.js.php");
    
    define("VIEWCONTROLLER","api/view/");
    define("USERCONTROLLER","api/user/");
    define("INSIDECONTROLLER","api/inside/");

    define("COST_PER_GAME",10);
    define("NBR_VALUES",50);
    define("NBR_OPERATORS",50);
    define("MAX_SAVERS",3);

    define("RNG_LOW_SPAN",array(2,10));                  //Range low.
    define("RNG_MID_SPAN",array(11,20));                 //Range mid.
    define("RNG_HIGH_SPAN",array(21,30));               //Range high.
    define("RNG_EXTREME_SPAN",array(50,100));         //Range extreme.
    define("RNG_PROBS_SPLIT",array(0.5,0.3,0.1,0.1)); //Probabilities for each split (LOW, MID, HIGH, EXTREME)
    define("RNG_PROBS_DEATH",array(0.4,0.3,0.3,0.5));   //Probabilities for getting death (LOW, MID, HIGH, EXTREME)
    
    define("RNG_PROBS_OP", array(0.25,0.25,0.25,0.25)); //Probability split on operators (+ - * /)
    define("RNG_PROBS_OP_DEATH", 1);
?>