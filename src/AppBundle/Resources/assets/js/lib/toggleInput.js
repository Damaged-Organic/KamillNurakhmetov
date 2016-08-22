"use strict";

export default class ToggleInput{
    constructor(){
        this.el = $(".toggle-input");
        this.UIevents();
    }
    UIevents(){
        this.el.on("blur", $.proxy(this.handleInput, this));
    }
    handleInput(e){
        var target = $(e.target);
		target.val().length > 0 ? target.addClass("filled") : target.removeClass("filled");
    }
}
