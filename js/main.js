jQuery(document).ready(function($){


// this site will be ajaxified, so we can't use regular selectors for event handlers
// and we need to have a refreshPage() function that takes care of any DOM manipulation

// ---------------------------------------
// HOW TO HANDLE EVENT HANDLERS
// ---------------------------------------

/* 

instead of $('img.gallery').on('click', ...);
use $(document).on('click', 'img.gallery', ...)

That way, when new elements are added to the page via AJAX, everything will still work.

*/

// ---------------------------------------
// FUNCTIONS THAT NEED TO FIRE ON PAGE LOAD
// ---------------------------------------

/*

Since we can't rely on $(document).ready() because we're using AJAX,
we use a custom queue object to load up all the stuff that needs to happen when the page loads.

Here's how: 

For this example function: 
function foo(){
	$('bar').hide();
}

Add this line to queue:
queue.enqueue(foo);

(queue refers to window.queue, defined right after this comment)
----------

when it's time to run the queue,

queue.run();

*/

window.queue = new Queue();



// ---------------------------------------
// -------- Home Page Navigation
// ---------------------------------------

// Keep it in the center of the page

$(window).on('load resize', function() {
	
	var winWidth = $(window).width();

	/* queue the banner width as a global variable, runs faster */
	if (typeof window.bannerWidth == 'undefined') window.bannerWidth = $('#banner').width();

	$('body.home #banner').css('left', (($(window).width() - window.bannerWidth)/2) + "px");

});

// Rollover animation

$(document).on('mouseenter', 'body.home #nav-main a', function() {
	var $target = $(this).parent("li");
	var targetid = $target.attr("id");
	
	$('<span></span>').hide().appendTo('#nav-main a').addClass('image').addClass(targetid).fadeIn(500);

}).on('mouseleave', 'body.home #nav-main a', function() {
	
	// fade out other images and remove them
	$('#nav-main .image').stop().fadeOut(500, function() { $(this).remove(); });

});


// ---------------------------------------
// -------- Inside Navigation
// ---------------------------------------

function alignNav() {
	if (jQuery.fn.vAlign) {
		$('#nav-interior li a span').vAlign();
	}
}
queue.enqueue(alignNav);

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
// -------- Photo Gallery
// ---------------------------------------

function photoGallery() {
	if ($('body.gallery').length && !$('#image-large').length) {

		$('#main').prepend('<div id="image-large"><span id="prev"></span><span id="next"></span><div id="active-caption"></div></div>');
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

	}
}

queue.enqueue(photoGallery);

// Adjust height of image enlargement to fit window	
$(window).on('resize load', function() {
	if ($('#image-large').length) {
		$('#image-large').height($('#gallery').offset().top - $('#wrap').offset().top-1);
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
// but this cornerstamp can be offset (e.g., not all the way to the right)

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
		filter += ".tag-" + $(this).text().toLowerCase();
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


