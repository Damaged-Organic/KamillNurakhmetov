"use strict";

import "jquery";
import "jquery-validation";
import "jquery-mask-plugin"; 
import EmulateTouch from "../lib/emulateTouchHover";
import ToggleInput from "../lib/toggleInput";
import AuthController from "../controllers/authController";
import Basket from "../controllers/basket";



$(function(){
    new ToggleInput();
    new AuthController();
    new Basket();

    let forms = $("form");

    forms.validate();
    
    forms.on("submit", function(e){
        if(!$(this).valid()) e.preventDefault();
    });

});
