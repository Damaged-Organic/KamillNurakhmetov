"use strict";

export default class ReviewController{

    constructor(){
        this.el = $("#review-form");
        this.responseHolder = $("#response-holder");

        this.UIevents();
    }
    UIevents(){
        this.el.on("submit", $.proxy(this.handleReview, this));
    }
    handleReview(e){
        e.preventDefault();

        if(!this.el.valid()) return;
        let formData = this.el.serializeArray(),
            path = this.el.attr("action");

        $.ajax({
            url: path,
            type: "POST",
            data: formData
        })
        .done((response) => {
            response = JSON.parse(response);

            this.el.addClass("hide");
            this.el[0].reset();
            this.responseHolder.html("<h3 class='success'>"+ response.message +"</h3>");
        })
        .fail((error) => {
            this.el.addClass("hide");
            this.el[0].reset();
            this.responseHolder.html("<h3 class='error'>"+ error.responseText +"</h3>");
        });

    }

}
