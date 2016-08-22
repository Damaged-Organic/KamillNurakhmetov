"use strict";

export default class ReaderController{

    constructor(){
        this.el = $("#reader-holder");
        this.wrapper = $("#page");
        this.footer = $("#footer");
        this.arrows = $(".arrow");

        this.UIevents();
    }
    UIevents(){
        this.wrapper
			.on("click touchstart", ".tool", $.proxy(this.handleReader, this))
			.on("click touchstart", ".contents-item", $.proxy(this.handleContents, this));

		$(window)
			.on("scroll", $.proxy(this.countScrollPercent, this))
			.on("resize", $.proxy(this.countScrollPercent, this));
    }
    handleReader(e){
		let target = $(e.target).closest(".icon-button");

		if(target.hasClass("contents")){

			this.wrapper.toggleClass("contents-active");
			$("body").toggleClass("no-scroll");

		} else if(target.hasClass("mode")){

			this.wrapper.toggleClass("mode-active");
		}

        return false;
    }
    handleContents(e){
		let target = $(e.target).closest(".contents-item"),
			contentsID = target.data("contents-id"),
            path = this.wrapper.data("path");

		if(target.hasClass("active")) return;

		this.el.empty();

		this.scrollPercents = 0;
		this.setPercentage();
		window.scrollTo(0, 0);

		this.wrapper.addClass("loading");
		target.siblings(".contents-item").removeClass("active");

        $.get(path, {contentsId: contentsID}).done((response) => {
            response = JSON.parse(response);

			this.wrapper.removeClass("loading contents-active");
			this.el.html(response.chapter);
			this.footer.find(".arrow.left > a").attr("href", response.prev_link);
			this.footer.find(".arrow.right > a").attr("href", response.next_link);
			target.addClass("active");
			$("body").removeClass("no-scroll");

        });

        return false;
    }
    countScrollPercent(e){
		let scrollY = $(window).scrollTop();

		this.scrollPercents = Math.round(scrollY / (this.wrapper.height() - $(window).innerHeight()) * 100);
		this.setPercentage();

		return false;
    }
    setPercentage(){
        this.footer.find(".read-percentage").html("<p>прочитано <span>"+ this.scrollPercents +"%</span></p>");
    }

}

/*(function($, root){

	"use strict";

	var ReaderService = require("../services/readerService");

	function ReaderController(){
		this.el = $("#reader-holder");
		this.initialize.apply(this, arguments);
	}
	ReaderController.prototype = {
		wrapper: [],
		footer: [],
		reader: {},
		scrollPercents: 0,
		initialize: initialize,
		_events: _events,
		handleReader: handleReader,
		handleContents: handleContents,
		countScrollPercent: countScrollPercent,
		setPercentage: setPercentage
	}

	function initialize(){
		this.wrapper = $("#page");
		this.footer = $("#footer");
		this.arrows = $(".arrow");
		this.reader = new ReaderService({ path: "php/reader.php" });

		this._events();
	}
	function _events(){
		this.wrapper
			.on("click touchstart", ".tool", $.proxy(this.handleReader, this))
			.on("click touchstart", ".contents-item", $.proxy(this.handleContents, this));

		$(root)
			.on("scroll", $.proxy(this.countScrollPercent, this))
			.on("resize", $.proxy(this.countScrollPercent, this));
	}
	function handleReader(e){
		e.preventDefault();

		var target = $(e.target).closest(".icon-button");

		if(target.hasClass("contents")){

			this.wrapper.toggleClass("contents-active");
			$("body").toggleClass("no-scroll");

		} else if(target.hasClass("mode")){

			this.wrapper.toggleClass("mode-active");
		}
	}
	function handleContents(e){
		e.preventDefault();

		var self = this,
			target = $(e.target).closest(".contents-item"),
			contentsID = target.data("contents-id");

		if(target.hasClass("active")) return;

		this.el.empty();

		this.scrollPercents = 0;
		this.setPercentage();
		root.scrollTo(0, 0);

		this.wrapper.addClass("loading");
		target.siblings(".contents-item").removeClass("active");

		this.reader.getContents(contentsID).done(function(contents){
			contents = JSON.parse(contents);

			self.wrapper.removeClass("loading contents-active");
			self.el.html(contents.chapter);
			self.footer.find(".arrow.left > a").attr("href", contents.prev_link);
			self.footer.find(".arrow.right > a").attr("href", contents.next_link);
			target.addClass("active");
			$("body").removeClass("no-scroll");
		});
	}
	function countScrollPercent(e){
		console.log(e);
		var scrollY = $(root).scrollTop();

		this.scrollPercents = Math.round(scrollY / (this.wrapper.height() - $(root).innerHeight()) * 100);
		this.setPercentage();
		return false;
	}
	function setPercentage(){
		this.footer.find(".read-percentage").html("<p>прочитано <span>"+ this.scrollPercents +"%</span></p>");
	}

	module.exports = ReaderController;

})(jQuery, window);*/
