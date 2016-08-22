"use strict";

import jquery from "jquery";
import validate from "jquery-validation";
import touchHover from "../lib/emulateTouchHover";
import ToggleInput from "../lib/toggleInput";
import AuthController from "../controllers/authController";
import ReviewController from "../controllers/reviewController";
import SeoText from "../lib/seoText";

$(function(){
    new ReviewController();
    new ToggleInput();
    new AuthController();
    new SeoText();

    $("form").validate();
});
