"use strict";

let basketHolder = $("#basket-holder");
let totalPriceHolder = $("#total-price");
let cartWidgetInfo = $("#cart-widget > .info");

class Basket{

    constructor(){
        this._events();
    }
    _events(){
        basketHolder.on("click", ".counter-btn", (e) => { this.handleCounter(e) });
    }
    handleCounter(e){
        let target = $(e.target);
        let quantityHolder = target.closest(".counter-holder").find(".quantity");
        let priceHolder = target.closest(".basket-item").find(".price");
        let url = target.data("path");

        $.ajax({
            url: url,
            method: "GET",
            data: {}
        })
        .done((response) => {
            //response = JSON.parse(response);

            quantityHolder.html(response.quantity);
            priceHolder.html(response.itemPrice);
            totalPriceHolder.html(response.totalPrice);
            cartWidgetInfo.html(response.cartWidget);
        });

        return false;
    }

}

export default Basket;
