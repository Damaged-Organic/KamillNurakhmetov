"use strict";

export default class Auth{

    constructor(){
        this.el = $("#authorization-holder");
        this.activationButtons = $(".auth-action");
        this.UIevents();
    }
    UIevents(){
        this.activationButtons.on("click touchstart", $.proxy(this.handleButtons, this));
		this.el.on("click touchstart", $.proxy(this.handleClose, this));
    }
    handleButtons(e){
        let target = $(e.target).closest(".auth-action"),
			authAction = target.data("auth-action");

		if(!this.el.hasClass("active")) this.el.addClass("active");

		this.el
            .find("." + authAction)
            .addClass("active")
            .siblings(".inner")
            .removeClass("active");

        return false;
    }
    handleClose(e){
        let target = $(e.target);

		if(target.closest(".inner").length <= 0 || target.hasClass("close-holder") || target.parent().hasClass("close-holder")){
			this.el.removeClass("active");
		}
    }

}
