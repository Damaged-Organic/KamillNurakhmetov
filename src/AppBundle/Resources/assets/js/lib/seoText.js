"use strict";

export default class SeoText{

    constructor(){
        this.el = $("#expand-seo-text");
        this.UIevents();
    }
    UIevents(){
        this.el.on("click", this.toggleSeoText.bind(this));
    }
    toggleSeoText(e){
        this.el.closest(".seo-text").toggleClass("active");
        return false;
    }

}
