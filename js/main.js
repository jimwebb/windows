jQuery(document).ready(function($){

/*

// NOTES
// ---------------------------------------

// this site will be ajaxified, so we can't use regular selectors for event handlers
// and we need to have a refreshPage()-like function that takes care of any DOM manipulation


// HOW TO HANDLE EVENT HANDLERS
// ---------------------------------------

instead of $('img.gallery').on('click', ...);
use $(document).on('click', 'img.gallery', ...)

That way, when new elements are added to the page via AJAX, everything will still work.



// FUNCTIONS THAT NEED TO FIRE ON PAGE LOAD
// ---------------------------------------

Since we can't rely on $(document).ready() because we're using AJAX,
we use a custom queue object to load up all the stuff that needs to happen each time a new page loads.

Here's how -- 

For this example function: 
function myFunction(){
	$('bar').hide();
}

Add this line to queue it on each page load:
queue.enqueue(myFunction);

(queue refers to window.queue, an object instantiated right after this comment)
----------

when it's time to run the queue,

queue.run();

*/





/*---TESTING---
$('a').pjax('#main');
$(document)
  .on('pjax:start', function() { console.log('starting pjax...') })
  .on('pjax:end',   function() { console.log('page fetched…') });

--END TESTING---*/

// Set up queue. Make it part of the window object so it's global.
window.queue = new Queue();



// ---------------------------------------
// -------- Home Page Navigation
// ---------------------------------------

$(window).resize( function() {
	resize();
});

function resize() {
	if ($('body.home').length) {
		var winWidth = $(window).width();
		var headerWidth = Math.floor( winWidth - (($(window).width() / 10) * 2) );
		var boxSize = Math.floor( (headerWidth/3)-5 );
		$('header').css('width', headerWidth);
		$('#nav-main > ul > li').css({'width': boxSize, 'height': boxSize});
	}	
}

function homePage() {
	
	resize();
    
    if ($('body.home').length) {

		$('#nav-main > ul > li span.corp').cycle({
			fx: 'fade',
			random: 1,
			slideResize: 0,
			containerResize: 0,
			width: '100%',
			delay: 500
		});
		
		$('#nav-main > ul > li span.wed').cycle({
			fx: 'fade',
			random: 1,
			slideResize: 0,
			containerResize: 0,
			width: '100%',
			delay: 2400
		});
		
		$('#nav-main > ul > li span.spec').cycle({
			fx: 'fade',
			random: 1,
			slideResize: 0,
			containerResize: 0,
			width: '100%',
			delay: 1200
		});

	}
}

queue.enqueue(homePage);


$(document).on('mouseenter', 'body.home #nav-main a', function() {
	
	var $target = $(this).parent('li');
	var imgSrc = $target.attr('class');
	
	$('#nav-main > ul > li > a').each( function(index) {
		$('<img src="/img/'+imgSrc+index+'.jpg">').hide().addClass('image').appendTo(this).fadeIn(400);
	});

}).on('mouseleave', 'body.home #nav-main a', function() {
	
	// fade out other images and remove them
	$('#nav-main .image').stop().fadeOut(400, function() { $(this).remove(); });

});



// ---------------------------------------
// -------- Inside Navigation
// ---------------------------------------

// vertically align titles within the nav bar 

//removing this, might be better to just hard code for those nav items that fall on two lines (we already target them with other styles). This will avoid the awkward alignment on initial load before the function calculates and executes.

/*
function alignNav() {
	if (jQuery.fn.vAlign) {
		$('#nav-interior > ul > li > a span').vAlign();
	}
}
queue.enqueue(alignNav);
*/



// ---------------------------------------
// -------- Background images
// ---------------------------------------

function backgroundImages() {
	if ($('#page-header').length) {
		if ($.fn.backstretch != 'undefined') {
			$.backstretch( $('#page-header').data('url'), 
							{ speed: 500, 
							target: '#page-header',
							positionType: 'relative' } );
			$('#page-header').addClass('active');
	} else {
			// fallback without backstretch plugin
			$('#page-header').css('background-image', 'url(' + $('#page-header').data('url') + ')');
			$('#page-header').addClass('active');	
		}
	}
}
queue.enqueue(backgroundImages);



// ---------------------------------------
// -------- AJAXify site
// ---------------------------------------

//stop the page from jumping!
$.pjax.defaults.scrollTo = false;

$(document).on('click', 'nav a', function(e) {

// ready to start working on ajax? Comment or remove this line:
// return;


	e.preventDefault();

	var url = $(this).attr("href");
	var $clicked = $(e.target);

	// what got clicked?
	
	if ($clicked.closest('#nav-main').length || $clicked.closest('.nav-home').length || $clicked.closest('.menu-home').length) {
	
		// either a link to the home page, or on the home page, was clicked
		var target = "#wrap";

	} else if ($clicked.closest('#nav-interior').length) {

		// a top nav item got clicked
		var target = "#content-wrapper";
		
		// if top parent link is clicked remove all other active states and apply an active state to first secondary nav item
		if($(this).parent().closest('ul').length == 1 && !$(this).parent().is('.nav-corporate, .nav-weddings, .nav-events, .nav-company')) {
			$(this).parent().parent().find('ul').children().removeClass('current_page_ancestor');
			$(this).parent().find('ul').children(':first-child').addClass('current_page_ancestor');
			$(this).parent().parent().find('li').removeClass('current_page_ancestor current_page_item');
			$(this).parent().addClass('current_page_ancestor');
			
		} else if ($(this).parent().is('.nav-corporate, .nav-weddings, .nav-events, .nav-company')) {
			
			var subList = $(this).parent().find('.children');
			var childrenWidth = $(this).parent().find('.children li').length * 74;
			
			if ( subList.width() > 0 ) {
				subList.animate({width: '0px'}, 400);
				
			} else {
				$('ul.children').each(function() {
					$(this).animate({width: '0px'}, 400);
					$(this).parent().removeClass('current_page_ancestor current_page_item');
				});
				subList.animate({width: childrenWidth+'px'}, 400);
				$(this).parent().addClass('current_page_ancestor');
			}
			
		}

	
	// if tertiary nav item is clicked
	} else if ($clicked.closest('#nav-subsection').length) {
		
		// a sub nav item got clicked
		var target = "#main";
	
		// remove other active states of tertiary nav and apply active class to clicked nav item
		$(this).parent().parent().children().removeClass('current_page_item');
	
		$(this).parent().addClass('current_page_item');


	} else {

		// fallback for anything else
		var target = "#content-wrapper";
		$(this).parent().parent().children().removeClass('current_page_ancestor');
		$(this).parent().addClass('current_page_ancestor');
	}
	
	
	if(!$(this).parent().is('.nav-corporate, .nav-weddings, .nav-events, .nav-company')) {


		$.pjax({
			url: url,	
			container: target,
			timeout: 5000,
			fragment: target
		});
	
	}


});


$(document).on('pjax:start',function(e) { 

	// fade out the page
	$target = $(e.target);

	$target.animate( {opacity: 0} , 300, function () {
		// TODO: Add loading animation here
		})
});


$(document).on('pjax:end',function(e) { 
	
	console.log("pjax end");

	// TODO: Remove loading animation if one was added

	$target = $(e.target);
	
	// fade in the new page
	$target.stop().css('opacity', 0).animate( {opacity: 1} , 300);

	// run the "document's loaded" queue
	queue.run();
});



// ------ Update classes on <body> on any PJAX load 

$(document).on('pjax:success', function(event, data) {

	var bodytag = /<body.*?class=[\"\'](.*?)[\"\'].*?>/i;
	var body = bodytag.exec(data);

	if (body != null) {
		$("body").attr("class", body[1]);
	} 
	
});









// ---------------------------------------
// -------- Photo Gallery
// ---------------------------------------

function photoGallery() {
	if ($('body.gallery').length && !$('#image-large').length) {

		$('#main').prepend('<div id="image-large"><a href="#" class="show-caption">open</a><span id="prev"></span><span id="next"></span><div id="active-caption"></div></div>');
		$('#gallery ul a').prepend('<span class="mask"></span>');
		$('#gallery ul').width($('#gallery li:first').outerWidth(true) * $('#gallery li').length);
		
		// activate inflickity for the bottom banner
		window.myFlickity = new Inflickity( $('div#gallery')[0], {
		  // options
		  // you can overwrite these defaults as you like
		  clones: 2,
		  friction: 0.03,
		  maxContactPoints: 3,
		  offsetAngle: 0,
		  onClick: undefined,
		  animationDuration: 400,
		  // basically jQuery swing
		  easing: function( progress, n, firstNum, diff ) {
			return ( ( -Math.cos( progress * Math.PI ) / 2 ) + 0.5 ) * diff + firstNum;
		  }
		});
			
		// load the first image
		
		galleryImage($('#gallery a:first'));
		
		if ($('#image-large').length) {
			$('#image-large').height($('#gallery').offset().top - $('#wrap').offset().top-71);
		}
		
		$('.show-caption').toggle(function() {
			$(this).parent().find('#active-caption').animate({
				left: '0%'
			}, 300);
		}, function() {
			$(this).parent().find('#active-caption').animate({
				left: '-100%'
			}, 300);
		});

	}
}

queue.enqueue(photoGallery);

// Adjust height of image enlargement to fit window	
$(window).on('resize load', function() {
	if ($('#image-large').length) {
		$('#image-large').height($('#gallery').offset().top - $('#wrap').offset().top-71);
	}
});

// make enlargements work on click 

$(document).on('click','#gallery a',function(e) {
	e.preventDefault();	
	galleryImage($(this));
});


// forward and next buttons
$(document).on('click', '#image-large #next', function() {
	var $active = window.activeImage.closest('li');
	
	if ($active.is(':last-child')) {
		galleryImage($active.prevAll().last().find("a"));
	} else {
		galleryImage($active.next().find("a"));
	}
});

$(document).on('click', '#image-large #prev', function() {

	var $active = window.activeImage.closest('li');
	
	if ($active.is(':first-child')) {
		galleryImage($active.nextAll().last().find("a"));
	} else {
		galleryImage($active.prev().find("a"));
	}

});
	


function galleryImage($elem) {
	
	var img = $elem.attr('href');
	var $li = $elem.closest('li');
	
	window.activeImage = $elem;
	
	// modify image
	
	if ($.fn.backstretch != 'undefined') {
		$.backstretch( img, 
						{ speed: 500, 
						target: '#image-large',
						positionType: 'relative' } );
		$('#gallery .active').removeClass('active');
		var idx = $elem.closest('li').index() + 1;
		$('#gallery li:nth-child('+idx+')').addClass('active');
			
	} else {
		// fallback without backstretch plugin
		$('#page-header').css('background-image', 'url(' + img + ')');
		$('#gallery li a[href="'+img+'"]').closest('li').addClass('active');
		$elem.closest('li').addClass('active');
	}
	
	// change caption
	
	$('#active-caption').html($li.find('.caption').html());
	
	


	// scroll this element to the front
	
	var position = $elem.closest('li').position().left;
	var midline = $(window).width() / 2;
	position = midline - position - ($('#gallery li:first').outerWidth(true)/2);
	
	myFlickity.scrollTo(position, 500);

}





// ---------------------------------------
// -------- Lookbook images
// ---------------------------------------


// Isotope modification to allow cornerstamping

  $.Isotope.prototype._masonryResizeChanged = function() {
    return true;
  };

  $.Isotope.prototype._masonryReset = function() {
    // layout-specific props
    this.masonry = {};
    this._getSegments();
    var i = this.masonry.cols;
    this.masonry.colYs = [];
    while (i--) {
      this.masonry.colYs.push( 0 );
    }
  
    if ( this.options.masonry.cornerStampSelector ) {
      var $cornerStamp = this.element.find( this.options.masonry.cornerStampSelector ),
          stampWidth = $cornerStamp.outerWidth(true) - ( this.element.width() % this.masonry.columnWidth ),
          cornerCols = Math.ceil( stampWidth / this.masonry.columnWidth ),
          cornerStampHeight = $cornerStamp.outerHeight(true);

//      for ( i = Math.max( this.masonry.cols - cornerCols, cornerCols ); i < this.masonry.cols; i++ ) {

//	  var colOffset = Math.min( this.masonry.cols - cornerCols, this.options.masonry.cornerStampOffset );
      var colOffset = Math.max( 0, this.masonry.cols - cornerCols - 1);
      
      for ( i = colOffset; i < colOffset + cornerCols; i++ ) {
      console.log ("col " + i);
        this.masonry.colYs[i] = cornerStampHeight;
      }
      
      // move the cornerstamp to the correct spot; assumes it's absolutely positioned
      $cornerStamp.css("left", colOffset * this.masonry.columnWidth + "px");
      
    }
  };


// Activate masonry/isotope

function lookbook() {
	$('body.lookbook #content').isotope({
		itemSelector: '.type-lookbook',
		masonry: {
			columnWidth: 159,
			cornerStampSelector: '#main',
			cornerStampOffset: 4
			}
	});

	$('.fancybox').fancybox({
		openEffect	: 'elastic',
    	closeEffect	: 'elastic',
    	padding: 0,
    	beforeShow: function() {
    		this.title = $(this.element).next('.caption').html();
    		},
    	helpers: {
    		title: { type: 'inside' }
    		}
    	    	/* 
    	nextMethod : 'resizeIn',
        nextSpeed : 250,
        
        prevMethod : 'resizeOut',
        prevSpeed : 250
		*/

    	}
	);
}

queue.enqueue(lookbook);

// Make lookbook LIs filter results when clicked

$(document).on('click', 'body.lookbook #main li', function() {
	
	$(this).toggleClass("active");

	// find all the active LIs and concatenate their names for the filter
	var filter = "";
	
	$('#main li.active').each(function() {
		if (filter != "") filter += ',';
		filter += ".tag-" + $(this).text().toLowerCase().replace(/ /g, '-');
	});

	if (filter == "") filter = "*";
	
	$('body.lookbook #content').isotope({ filter: filter });

});



// Extra visual effects for fancybox

(function ($, F) {
    F.transitions.resizeIn = function() {
        var endPos   = $.extend({}, F.current.dim, F._getPosition(true));
        var startPos;
        
        startPos = F.tmpWrap.position();
        
        startPos.width  = F.tmpWrap.width();
        startPos.height = F.tmpWrap.height();

        F.inner
            .css('overflow', 'hidden')
            .width( F.tmpInner.width() )
            .height( F.tmpInner.height() )
            .css('opacity', 0);

        F.wrap.css(startPos).show();

        F.tmpWrap.trigger('onReset').remove();

        F.wrap.animate(endPos, {
            duration: F.current.nextSpeed,
            step    : F.transitions.step,
            complete: function() {
                setTimeout(function() {
                    F.inner.fadeTo("fast", 1, F._afterZoomIn);
                }, 1);
            }
        });
    };
 
    F.transitions.resizeOut = function() {
        if (F.tmpWrap) {
            F.tmpWrap.stop(true).trigger('onReset').remove();
        }
                       
        F.tmpWrap  = F.wrap.stop(true, true);
        F.tmpInner = F.inner.stop(true, true);
    };

}(jQuery, jQuery.fancybox));






// ---------------------------------------
// -------- Contact Form
// ---------------------------------------

// We use fancy footwork here to split the contact form
// into multiple columns by section break

function contactForm() {

	$('.gform_body li.gsection:eq(1)').each(function() {

		var $col = $(this).nextAll().andSelf();
		var $ul = $(this).closest("ul").addClass("col");
		$ul.after("<ul></ul>").next("ul").addClass($ul.attr("class")).addClass("last").append($col);

	});


	$('.gf_list_2col ul').each(function() {
		
		var $ul = $(this);
		var $lis = $ul.find('li');

		var numitems = $lis.length;
		var $col = $lis.slice(Math.floor(numitems/2));
		$ul.addClass("col").after("<ul></ul>").next("ul").addClass($(this).attr("class")).addClass("last").append($col);

	});

}

queue.enqueue(contactForm);


// --------------------------------------
// ------ Run the queue -- this should be last
// --------------------------------------

queue.run();

});


