"use strict";

import jquery from "jquery";
import validate from "jquery-validation";
import touchHover from "../lib/emulateTouchHover";
import ToggleInput from "../lib/toggleInput";
import AuthController from "../controllers/authController";

$(function(){

    new ToggleInput();
    new AuthController();

    $("form").validate();
});
