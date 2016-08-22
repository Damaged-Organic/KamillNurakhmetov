"use strict";

export default class StoriesController{

    constructor(){
        this.el = $("#stories-list");
        this.reviewHolder = $("#review-holder");
        this.inputStorieId = $("#review-storie-id");

        this.UIevents();
    }
    UIevents(){
        this.el.on("click", ".review-button", $.proxy(this.openReviews, this));
		this.reviewHolder.on("click", ".close-button", $.proxy(this.closeReviews, this));
    }
    openReviews(e){
        if(this.reviewHolder.hasClass("active")) return;
		let storieID = $(e.currentTarget).data("storie-id"),
            isFormVisible = $(e.currentTarget).data("form-visible");

        isFormVisible ? this.reviewHolder.removeClass("no-form") : this.reviewHolder.addClass("no-form");

		this.reviewHolder
			.addClass("active")
			.find(".storie-id-" + storieID)
			.addClass("active")
			.siblings()
			.removeClass("active");

		this.reviewHolder.find("#review-form").removeClass("hide");
		this.reviewHolder.find("#response-holder").empty();
		this.inputStorieId.val(storieID);

        return false;
    }
    closeReviews(e){
        this.reviewHolder.removeClass("active");

        return false;
    }
}
