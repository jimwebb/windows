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

window.pjaxStates = {};


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

    	// load images (they're set up as data-src so they don't load on inside pages)
		$('.home-slideshow img').each(function() {
			var $this = $(this);
			$this.attr('src', $this.attr('data-src'));
		});

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


var width = 40;
$('.footer-wrap').children().each(function() {
    width += $(this).outerWidth( true );
});

$('.footer-wrap').width(width);



// ---------------------------------------
// -------- Dropdown Navigation
// ---------------------------------------

//open the dropdown nav if we are on it's page;


function dropNav() {
	if($('.current_page_item').parent().hasClass('dropdown') ) {
		
		$('.current_page_item').parent().show(200);
		
	}
}
queue.enqueue(dropNav);


// ---------------------------------------
// -------- Main Navigation
// ---------------------------------------

// Set up main navigation CSS

/*
old attempt with left positioning -- ok to delete

function setupNav() {

	if ($('#nav-interior').length) {
		var width = 0;
		var maxwidth = 0;

		// set the left position of each menu item
		// (they're absolutely positioned)

		$('#nav-interior > ul > li').each(function() {
			$this = $(this);

			// set the left property to the culmulative width
			$this.css('left', width + 'px');

			// the width of each menu item is itself, minus any child ULs
			// add it to the cumulative total for the next item in the loop
			elwidth = $this.width();
			width += elwidth - $this.find('ul').width();

			// maxwidth is the maximum width, including offset, of each opened menu item
			if ((elwidth + width) > maxwidth) maxwidth = elwidth + width;

		});


		// give each menu item right-padding so each will cover the ones underneath

		$('#nav-interior > ul > li').each(function() {


		});


	}

}
*/


function setupNav() {
	if ($('body.home').length) return; 

	if (!$('#nav-interior.rendered').length) {
		
		// $('#nav-interior').css('position', 'relative').css('left', '-9999px'); // FOUC
		

		// console.log ('time to render the nav!');


		$('#nav-interior > ul > li').each(function() {
			
			var $this = $(this);
			var $subnav = $this.find('ul:first');

			if ($subnav.length) {
				// introduce a container element so we can do the sliding effect properly
				$this.wrapInner('<div class="nav-container"></div>');
			
				// set the subnav to its proper natural width
				// because we're using overflow:hidden to hide it

				var subnavWidth = $subnav.width();
				$subnav.width(subnavWidth);

				// set the menu width to itself, minus the subnav
				var navWidth = $this.find('a:first').outerWidth(true);

				// console.log("resetting nav", $this, navWidth, subnavWidth);
				$this.find('.nav-container').width( navWidth + subnavWidth + 10); // 10 extra for safety!
				$this.width(navWidth);

			}
		
		});
	
	$('#nav-interior').hide().addClass('rendered').css('position','static').css('left','0px').fadeIn(100);

	setNav(false);

	} else {

	setNav (true);

	}

}

queue.enqueue(setupNav);


// setNav : determines which subnav is currently active, then expands to show it

function setNav(animate, $clicked) {

	if ($('body.home').length) return; 

	// has the nav been set up? if not, set up the nav.
	if (!$('#nav-interior.rendered').length) setupNav(); 

	// set "animate" to default true and el to false

	var animate = typeof animate !== 'undefined' ? animate : true;
	var $clicked = typeof $clicked !== 'undefined' ? $clicked : [];


	// reset classes properly

	if ($clicked) {
		// console.log ("clicked is non-false: ", $clicked);

		if (!$clicked.length || !$clicked.is('a')) {

			$clicked = []; // reset

			// we don't know what was clicked. we have to find it ourselves.

			var pathname = stripTrailingSlash(location.pathname);

			var $navlinks = $('#nav-interior li li a');

			// first try the path as is 
			$clicked = testPathName (pathname, $navlinks);

			// now removed one level
			if (!$clicked || !$clicked.length) {
				pathname = stripTrailingSlash(pathname.substring(0, pathname.lastIndexOf("/")))
				$clicked = testPathName (pathname, $navlinks);
			}

			// now removed two levels
			
			if (!$clicked || !$clicked.length) {
				pathname = stripTrailingSlash(pathname.substring(0, pathname.lastIndexOf("/")))
				$clicked = testPathName (pathname, $navlinks);
			}

		}
		

		if (!$clicked || !$clicked.length || !$clicked.is('#nav-interior a')) {
			
			// if this is an archive or single post page, we're in the "what's new" section.

			if ($('body.archive').length || $('body.single').length) {
				$clicked = $('#nav-interior li.menu-whats-new a');
			} else {
			// we stiiiiiiilll don't have it? give up.
			return false;
			}
		} 

		$li = $clicked.closest('li');


		// is this link already the active one?
		// if ($li.is('.current_page_ancestor, .current_page_parent, .current_page_item')) return;

		// remove existing current_page_ancestors
		$('#nav-interior li.current_page_ancestor, #nav-interior li.current_page_parent, #nav-interior li.current_page_item').removeClass('current_page_ancestor current_page_parent current_page_item');

			//console.log('the closestest li', $li);

		// add current_page_ancestor and current_page_parent to this li and to the parent li
		$li.addClass('current_page_item').parent().closest('#nav-interior li').addClass('current_page_ancestor current_page_parent');
	}


	// set the width of the current li to show what's inside

	var $currentitem = $('#nav-interior > ul > li.current_page_ancestor');
	var $olditem =  $('#nav-interior > ul > li.active');

	// Check if the top-level category changed 
	if ($currentitem[0] != $olditem[0]) {

		var width = $currentitem.find('.nav-container').width()

		var oldwidth = $olditem.find('.nav-container > a').outerWidth(true);
		
		// console.log ('width', width, $currentitem, $currentitem.find('.nav-container'));

		if (animate) {
			$olditem.animate({width: oldwidth + 'px'}, 300).removeClass('active');
			$currentitem.animate({width: width + 'px'}, 400).addClass('active');
		} else {
			$currentitem.width(width).addClass('active');
			$olditem.width(oldwidth).removeClass('active');
		}

	}


}


// testPathName: utility function to compare a test pathname against a jQuery collection of links
// returns the jquery object (a tag) that has the same href as the pathname

function testPathName (pathname, $links) {

			if (typeof pathname == 'undefined' || typeof $links == 'undefined') return;

			var pathname = stripTrailingSlash(pathname);
			var $found = false;

			$links.each(function() {
				$this = $(this);
				var testpath = stripTrailingSlash($this.attr('href'));
				// console.log ('comparing: ', testpath, pathname, testpath == pathname, $this);
				if (testpath == pathname) $found = $this;
			});

			// if we made it this far, nothing was found.
			return $found;
}



// ---------------------------------------
// -------- Background images
// ---------------------------------------

function backgroundImages() {
	if ($('#page-header').length) {

		// make sure we don't already have this image showing
		var image = $('#page-header').data('url');

		if (typeof window.backstretch !== "undefined" && window.backstretch == image) {
			var speed = 0; } 
			else {
			var speed = 500;
			}

		if ($.fn.backstretch != 'undefined') {
			$.backstretch( image, 
							{ speed: speed, 
							target: '#page-header',
							positionType: 'relative' } );
			$('#page-header').addClass('active');
			window.backstretch = image;
		} else {
			// fallback without backstretch plugin
			$('#page-header').css('background-image', 'url(' + image + ')');
			$('#page-header').addClass('active');	
			window.backstretch = image;
		}
	}
}
queue.enqueue(backgroundImages);



// ---------------------------------------
// -------- AJAXify site
// ---------------------------------------


// utility function to store elements of the page to make the forward and backward buttons work

function storePjaxState() {
		if (typeof $.pjax.state !== 'undefined' && typeof window.pjaxStates['id' + $.pjax.state.id] == 'undefined') {

			// console.log("storing pjaxstate", $.pjax.state.id, $('body').attr('class'), $('#nav-interior .current_page_item a')[0] || $('#nav-interior .current_page_parent a')[0]);

			window.pjaxStates['id' + $.pjax.state.id] = { 
				'bodyclass' : $('body').attr('class'),
				'navitem' : $('#nav-interior .current_page_item a')[0] || $('#nav-interior .current_page_parent a')[0]
			}
		}
}

queue.enqueue(storePjaxState);


//stop the page from jumping!

if ($.support.pjax) $.pjax.defaults.scrollTo = false;


$(document).on('click', 'nav a, body.archive #main header a', function(e) {

// ready to start working on ajax? Comment or remove this line:
// return;

	if(!$(this).hasClass('no-pjax') && !$(this).hasClass('has_drop')) {
	
		e.preventDefault();
	
	}

	var url = $(this).attr("href");
	var $clicked = $(e.target);

	if (!$clicked.is('a')) $clicked = $clicked.closest('a');

	// console.log ($clicked);

	// what got clicked?
	
	if ($clicked.closest('#nav-main').length || $clicked.closest('#nav-utility').length || $clicked.closest('.nav-home').length || $clicked.closest('.menu-home').length) {
	
		// either a link to the home page, or on the home page, was clicked
		var target = "#wrap";

	} else if ($clicked.closest('#nav-interior').length) {

		// a top nav item got clicked
		var target = "#content-wrapper";
	
		// is this link a top-level link?
		// if so, let's use the first menu item instead
		if (!$clicked.is('#nav-interior li li a') && $clicked.closest('li').find('ul').length) {
			$clicked = $clicked.closest('li').find('ul li:first a');
			url = $clicked.attr("href")
		}
	
	// if tertiary nav item is clicked
	} else if ($clicked.closest('#nav-subsection').length && !$clicked.hasClass('no-pjax')) {
		
		// a sub nav item got clicked
		var target = "#main";
	
		// remove other active states of tertiary nav and apply active class to clicked nav item
		// no longer necessary -- pjax swaps out the content
		/* 
		$(this).parent().parent().parent().parent().find('li').removeClass('current_page_item current-cat');
	
		$(this).parent().addClass('current_page_item');
		
		$(this).parent().parent().find('ul.dropdown').hide(200);
		*/

	} else if ($clicked.closest('.post').length) {
	
		var target = "#main";	

	// if normal link
	} else if ($clicked.hasClass('no-pjax') && !$clicked.hasClass('has_drop')) {
		return true;
	
	// if tertiary drop down
	} else if ($clicked.hasClass('has_drop')) {
	
		e.preventDefault();
		
	    $(this).parent().find('ul').slideToggle(200);
		
	} else {

		// fallback for anything else
		var target = "#content-wrapper";

		/*
		$(this).parent().parent().children().removeClass('current_page_ancestor');
		$(this).parent().addClass('current_page_ancestor');
		*/
	}


	// we're always going to swap out the same container and target, because
	// pjax can't handle different containers/targets for different pages.
	// but we save which target is actually switching, so we can fade it in/out properly.

	window.$pjaxtarget = $(target);
	window.$clicked = $clicked;

	if (!$(this).hasClass('no-pjax')) {
		// load the content
		$.pjax({
			url: url,	
			container: '#content-wrapper',
			timeout: 5000,
			fragment: '#content-wrapper'
		});
	
	}


	// animate the nav 
	// 
});




$(document).on('pjax:start',function(e) { 

	// fade out the page
	$target = window.$pjaxtarget;
	if (typeof $target === 'undefined') $target = $(e.target);

	$target.animate( {opacity: 0} , 300, function () {
		// TODO: Add loading animation here
		})
	
	// console.log("before starting, pjax state", $.pjax.state, $.pjax.state.bodyclass);
	storePjaxState();

	setNav(true, window.$clicked);
	// console.log ('reset nav on click', $clicked);

	// console.log("pjaxStates", window.pjaxStates);

});

$(document).on('pjax:end pjax:popstate',function(e, d) { 
	
	// TODO: Remove loading animation if one was added

	$target = window.$pjaxtarget;
	if (typeof $target === 'undefined') $target = $(e.target);

	window.$clicked = null;

	// reset the nav if this is a forward/back
	if (e.type == 'pjax:popstate') setNav(true, false);

	$target.stop().css('opacity', 0);

	// if body and nav states exist, use 'em

	// console.log('looking for state id ' + $.pjax.state.id);

	if (typeof $.pjax.state !== 'undefined' && typeof $.pjax.state.id !== 'undefined' && typeof window.pjaxStates['id' + $.pjax.state.id] !== 'undefined') {
		$('body').attr('class', window.pjaxStates['id' + $.pjax.state.id]['bodyclass']);
		setNav(true, $(window.pjaxStates['id' + $.pjax.state.id]['navitem']) );
	}

	
	// fade in the new page
	$target.animate( {opacity: 1} , 300);


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
	
	// save the body classes into the target element, for retrieval during popstate
	$(event.target).children(":first").attr('data-bodyclass',body[1]);

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
			$('#image-large').height($('#gallery').offset().top - $('#wrap').offset().top-86);
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
		$('#image-large').height($('#gallery').offset().top - $('#wrap').offset().top-86);
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
      // console.log ("col " + i);
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
			},
		onLayout: function() {
			$(".isotope-item .fancybox").attr("rel","lookbook");
			$(".isotope-item.isotope-hidden .fancybox").attr("rel","");
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

	$('.gform_body ul:not(".col") li.gsection:eq(1)').each(function() {

		var $col = $(this).nextAll().andSelf();
		var $ul = $(this).closest("ul").addClass("col");
		$ul.after("<ul></ul>").next("ul").addClass($ul.attr("class")).addClass("last").append($col);

	});


	$('.gf_list_2col ul:not(".col")').each(function() {
		
		var $ul = $(this);
		var $lis = $ul.find('li');

		var numitems = $lis.length;
		var $col = $lis.slice(Math.floor(numitems/2));
		$ul.addClass("col").after("<ul></ul>").next("ul").addClass($(this).attr("class")).addClass("last").append($col);

	});

}

queue.enqueue(contactForm);




// ---------------------------------------
// -------- Disable attachment links
// ---------------------------------------

function disableAttachmentLinks() {
	console.log('disabler');
	$('body.single-post a[rel*="attachment"], body.blog a[rel*="attachment"], body.archive a[rel*="attachment"]').on('click', function(e) {
		e.preventDefault(); return false;
		console.log('clicked');
	})
}
// queue.enqueue(disableAttachmentLinks);


function fancyboxAttachmentLinks() {

$('body.single-post a[rel*="attachment"], body.blog a[rel*="attachment"], body.archive a[rel*="attachment"]').fancybox({
		openEffect	: 'elastic',
    	closeEffect	: 'elastic',
    	type: 'image',
    	padding: 0,
    	helpers: {
    		title: { type: 'inside' }
    		}
    	});
}

queue.enqueue(fancyboxAttachmentLinks);



// --------------------------------------
// ------ Run the queue -- this should be last
// --------------------------------------

queue.run();

});


// utility function to strip trailing slashes

function stripTrailingSlash(str) {
    if(str.substr(-1) == '/') {
        return str.substr(0, str.length - 1);
    }
    return str;
}


