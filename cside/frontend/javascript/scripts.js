/*!
 * Scripts
 *
 */
head.ready(function() {
 "use strict";

	var Engine = {
		utils : {
			links : function(){
				$('a[rel*="external"]').click(function(e){
					e.preventDefault();
					window.open($(this).attr('href'));
				});
			},
			mails : function(){
				$('a[href^="mailto:"]').each(function(){
					var
						mail = $(this).attr('href').replace('mailto:',''),
						replaced = mail.replace('/at/','@');
					$(this).attr('href','mailto:' + replaced);
					if($(this).text() === mail) {
						$(this).text(replaced);
					}
				});
			}
		},
		forms : {
			fields : function() {
				$('input, textarea').placeholder();
			}
		},
		ui : {
			tables : function() {
				$('.table-a table').each(function(){
					var $root         = $(this);
					var rootHeight    = $(this).outerHeight();
					var rootPos       = $root.position();

					// add arrow & set position
					$root.find('tbody tr').each(function(){

						// add arrow to overlay
						var $overlay  = $(this).find('.overlay');
						var $arrow    = $('<span class="overlay-arrow"></span>');
						$overlay.prepend($arrow);

						// set position
						var thisPos   = $(this).position();
						var arrowPos  = thisPos.top - rootPos.top;
						var criticalH = $overlay.outerHeight() - 35;

						if ( thisPos.top < criticalH ) {
							$arrow.css('top', arrowPos);
						} else {
							$arrow.hide();
						}
					});

				});
			},
			tooltip : function() {
				$('.tooltip-a').tooltip();
			}
		},
		fixes : {
			enhancements : function() {
				if(!$.support.leadingWhitespace){
					$('hr').wrap('<div class="hr"></div>');
					$(':last-child:not(cufon)').addClass('last-child');
				}
			},
			pie : function() {
				$('body').bind('refresh.pie',function() {
					if(!$.support.leadingWhitespace){
						if(window.PIE !== undefined){
							$('.INSERT_PIE_ELEMENTS_HERE').each(function() {
								window.PIE.detach(this);
								window.PIE.attach(this);
							});
						}
					}
				});
			}
		}
	};

	Engine.utils.links();
	Engine.utils.mails();
	Engine.forms.fields();
	//Engine.ui.tables();
	Engine.ui.tooltip();
	Engine.fixes.enhancements();

});