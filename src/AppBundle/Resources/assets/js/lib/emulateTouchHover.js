"use strict";

import {isMobile} from "./isMobile";

const IS_MOBILE = isMobile();

class TouchHover{
    constructor(){
        if(!IS_MOBILE) return;
        this.el = $(".touch-hover");
        this.UIevents();
    }
    UIevents(){
        this.el
			.on("touchstart", this.handleTouchStart)
			.on("touchend", this.handleTouchEnd);
    }
    handleTouchStart(e){
        $(this).removeClass("touch-hover-end").addClass("touch-hover-start");
    }
    handleTouchEnd(e){
        $(this).removeClass("touch-hover-start").addClass("touch-hover-end");
    }
}

export default new TouchHover();
