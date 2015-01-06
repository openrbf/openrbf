/*
 * agility.js
 * 
 * The main javascript for Agility - Responsive HTML5/CSS3 Template by SevenSpark
 * 
 * Copyright 2011 Chris Mavricos, SevenSpark
 * http://sevenspark.com
 * 
 */

jQuery(document).ready(function($){
	
	/* Style for JS-enabled */
	$('body').addClass('js-enabled');	
	
	/* Keep track of the width for use in mobile browser displays */
	var currentWindowWidth = $(window).width();
	$(window).resize(function(){
		currentWindowWidth = $(window).width();
	});
	
		
	/* MOBILE MENU */
    $('.mobile-menu-button').click(function(e){
    	e.preventDefault();
    	var $menu = $($(this).attr('href'));
    	$menu.toggleClass('menu-open'); //toggle()
    	
    	if(typeof $navClose !== 'undefined' && !$menu.hasClass('menu-open') ){
    		console.log('hide');
    		$navClose.hide();
    	}
    });
	
	/* Expander for featured images */
	$('.single-post-feature-expander').click(function(){
		$('.featured-image').toggleClass('full-width');
	});
	
	//Size images in IE
	imgSizer.collate();
	
    
    //IPHONE, IPAD, IPOD
    var deviceAgent = navigator.userAgent.toLowerCase();    
	var is_iOS = deviceAgent.match(/(iphone|ipod|ipad)/);
	
	if (is_iOS) {
        
        $('#main-nav').prepend('<a href="#" class="nav-close">&times;</a>'); // Close Submenu
        
        var $navClose = $('.nav-close');
        $navClose.hide().click(function(e){
        	e.preventDefault();
        	if(currentWindowWidth >= 767){
        		$(this).hide();
        	}
        });
		
        $('#main-nav > ul > li').hover(function(e){
        	e.preventDefault();
        	if(currentWindowWidth < 767){
        		$navClose.css({ 
        			top : $(this).position().top + 33,
        			left : '',
        			right : 0
        		}).show();
        	}
        	else{
        		$navClose.css({
        			left : $(this).position().left + parseInt($(this).css('marginLeft')),
        			top : '',
        			right : 'auto'
        		}).show();
        	}
        });
              
	}
	
	
	//NON-IOS
	if(!is_iOS){
		//iOS doesn't like CSS3 transitioning preloader, so don't use it
		$('.preload').preloadImages({
	        showSpeed: 200   // length of fade-in animation, should be .2 in CSS transition
	    });	   
		$('.video-container').addClass('video-flex');
	}
	
	
	//ANDROID
	var is_Android = deviceAgent.match(/(android)/);
	if(is_Android){
		//Do something special with Android
	}
    
        
    //IE automatic grid clears
    if($.browser.msie){
		$('.portfolio.col-4 article:nth-child(4n+1)').addClass('clear-grid');
		$('.portfolio.col-3 article:nth-child(3n+1)').addClass('clear-grid');
		$('.portfolio.col-2 article:nth-child(2n+1)').addClass('clear-grid');
	}
	
		
});



//Image Preloader
jQuery.fn.preloadImages = function(options){

    var defaults = {
        showSpeed: 200
    };

    var options = jQuery.extend(defaults, options);

	return this.each(function(){
		var $container = jQuery(this);
		var $image = $container.find('img');

		$image.addClass('loading');	//hide image while loading
         
		$image.bind('load error', function(){
			$image.removeClass('loading'); //allow image to display (will fade in with CSS3 trans)
			
			setTimeout(function(){ 
				$container.removeClass('preload'); //remove the preloading class to swap the bkg
			}, options.showSpeed);
			
		});
		
		if($image[0].complete || (jQuery.browser.msie )) { 
			$image.trigger('load');	//IE has glitchy load triggers, so trigger it automatically
		}
    });
}


/* IE Image Resizing - by Ethan Marcotte - http://unstoppablerobotninja.com/entry/fluid-images/ */
var imgSizer = {
	Config : {
		imgCache : []
		,spacer : "../images/spacer.gif"
	}

	,collate : function(aScope) {
		var isOldIE = (document.all && !window.opera && !window.XDomainRequest) ? 1 : 0;
		if (isOldIE && document.getElementsByTagName) {
			var c = imgSizer;
			var imgCache = c.Config.imgCache;

			var images = (aScope && aScope.length) ? aScope : document.getElementsByTagName("img");
			for (var i = 0; i < images.length; i++) {
				images[i].origWidth = images[i].offsetWidth;
				images[i].origHeight = images[i].offsetHeight;

				imgCache.push(images[i]);
				c.ieAlpha(images[i]);
				images[i].style.width = "100%";
			}

			if (imgCache.length) {
				c.resize(function() {
					for (var i = 0; i < imgCache.length; i++) {
						var ratio = (imgCache[i].offsetWidth / imgCache[i].origWidth);
						imgCache[i].style.height = (imgCache[i].origHeight * ratio) + "px";
					}
				});
			}
		}
	}

	,ieAlpha : function(img) {
		var c = imgSizer;
		if (img.oldSrc) {
			img.src = img.oldSrc;
		}
		var src = img.src;
		img.style.width = img.offsetWidth + "px";
		img.style.height = img.offsetHeight + "px";
		img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "', sizingMethod='scale')"
		img.oldSrc = src;
		img.src = c.Config.spacer;
	}

	// Ghettomodified version of Simon Willison's addLoadEvent() -- http://simonwillison.net/2004/May/26/addLoadEvent/
	,resize : function(func) {
		var oldonresize = window.onresize;
		if (typeof window.onresize != 'function') {
			window.onresize = func;
		} else {
			window.onresize = function() {
				if (oldonresize) {
					oldonresize();
				}
				func();
			}
		}
	}
}


