/*
 * Sweden Unlimited Javascript
 *
 * Donuts & Coffee included.
 */

/*
 * Rich Cart Tooltip
 */
(function() {
  
    jQuery(document).ready(function($) {
    
    $('.rich-cart .content .item').hover(function() {
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    }).bind('mousemove', function(e) {
      
      var top = ( e.pageY - $(this).offset().top );
      var left = ( e.pageX - $(this).offset().left );
      
      $(this).find('.tooltip').css({
        'top': top - 10,
        'left': left + 25
      })
      
    });
    
  });
  
})();


// updates the cart after you add to cart (ajax cart pro) 
function fixRichCart() {
    jQuery('.rich-cart .content .item').hover(function() {
      jQuery(this).addClass('hover');
    }, function() {
      jQuery(this).removeClass('hover');
    }).bind('mousemove', function(e) {
      var top = ( e.pageY - jQuery(this).offset().top );
      var left = ( e.pageX - jQuery(this).offset().left );
      
      jQuery(this).find('.tooltip').css({
        'top': top - 10,
        'left': left + 25
      })
      
          });

//    alert( "hm" );
    var $cart = jQuery('.rich-cart-container');
    $cart.find('.close').bind('click', function(e) {
      e.preventDefault();
//      alert( "hm2" );
      
      var $cart = jQuery('.rich-cart-container');
      $cart.trigger('rise');
    });

}

/*
 * Product Pg Accordion
 */
(function() {
	
	jQuery(document).ready(function($){
	
	   $('.product-info .accordion_container').accordion( {
		   	header: '.trg',
			active: '.open',
			alwaysOpen: false,
			autoheight: false
	   });
	  
	});

})();


/*
 * Product Also Avaliable Sliding on Hover
 */
(function() {
  
    jQuery(document).ready(function($) {
    
    var $also_available = $('.catalog-product-view .also-available');
    var $product = $('.catalog-product-view .product');
    var num_of_columns = $also_available.find('.column').length;

    var collapsed_width = $also_available.width();
    var expanded_width = (num_of_columns*75) + ((num_of_columns-1)*10);
    
    var easing = 'easeOutQuint';
    var time = 300;
    
    $also_available.width( expanded_width );
    $also_available
      .css({
        'right': collapsed_width - expanded_width
      });
    
    $('.product')
      .delegate('.also-available', 'mouseenter', function() {
        $(this)
          .stop(true,true)
          .animate({
            'right': 0
          }, time, easing, function() {
            $(this).addClass('expanded');
            $also_available.add( $product ).css('overflow','visible');
          });
      })
      .delegate('.also-available', 'mouseleave', function() {
        $also_available.add( $product ).css('overflow','hidden')

        $(this)
          .stop(true,true)
          .removeClass('expanded')
          .animate({
            'right': collapsed_width - expanded_width
          }, time, easing, function() {
            $(this).removeClass('expanded');
          });
		 $also_available.add( $product ).css('overflow','hidden')
      });
    
  });
  
})();

/*
 * Also Avaliable Tooltip
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    $('.catalog-product-view .also-available .item').hover(function() {
      if( !$(this).parents('.also-available').eq(0).hasClass('expanded') ) { return false; }
      
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    }).bind('mousemove', function(e) {
      if( !$(this).parents('.also-available').eq(0).hasClass('expanded') ) { return false; }
      
      var top = ( e.pageY - $(this).offset().top );
      var left = ( e.pageX - $(this).offset().left );
      
      $(this).find('.tooltip').css({
        'top': top - 10,
        'left': left + 30
      })
      
    });
    
  });
  
})();  

/*
 * Catalog Pattern Tooltip
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    $('.product-listing .swatch').hover(function() {
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    }).bind('mousemove', function(e) {
      var top = ( e.pageY - $(this).offset().top );
      var left = ( e.pageX - $(this).offset().left );
      
      $(this).find('.tooltip').css({
        'top': top - 10,
        'left': left + 20
      })
      
    }).each(function() {
      var $tooltip = $(this).find('.tooltip');
      $tooltip.wrapInner('<span class="inside"></span>');
      
      $tooltip.find('.inside').width( $tooltip.width() );
    });
    
  });
  
})();


/*
 * Catalog Item Shopping Overlay
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    var time = 0;
    var delay = 0;

    var tallestonrow = 0;
    var rowbeginning = 0;
    
    $('.product-listing .product').each(function(index) {

      // explicitly set height of items for IE7
      if (jQuery.browser.msie && parseInt(jQuery.browser.version,10) < 8)
      {   
          if ($(this).height() > tallestonrow)
            tallestonrow = $(this).height();
          if ($(this).hasClass('beginning'))
          {
            for (var j = index-1; j > rowbeginning; j--)
            {
              $('.product-listing .product').eq(j).height(tallestonrow);
            }
            tallestonrow = 0;
            rowbeginning = index; 
          }
      }
      
      var $elems = $(this).find('.tooltip,img.alt');
      var timeout = null;
      
      $(this).hover(function() {
        var $this = $(this);
        
        timeout = setTimeout(function() {
          if (jQuery.browser.msie && parseInt(jQuery.browser.version,10) < 9)
            $elems.show();
          else
            $elems.stop(true,true).fadeTo( time, 1 );
        }, delay);
        
      }, function() {
        clearTimeout( timeout );
        if (jQuery.browser.msie && parseInt(jQuery.browser.version,10) < 9)
          $elems.hide();
        else
          $elems.stop(true,true).fadeTo( time, 0 );
      });
      
      if (!jQuery.browser.msie || parseInt(jQuery.browser.version,10) >= 9)
        $elems.fadeTo(0,0);

      // explicitly remove alt, title tags for rollover
      $(this).find('img').attr('alt','');
      $(this).children('a').attr('title','');
    
    });
    
  });
  
})();


/*
 * Catalog Product Pager
 */
(function() {

  function simulatedSwap(clickeditem)
  {
    if (currswapping) return;
    currswapping = true;
    clickeditem.trigger('click');
    var $magictoolbox = jQuery('.MagicToolboxContainer');

    var newimgsrc = clickeditem.attr('rev');
    var newimgsrcfull = clickeditem.attr('href');
    var oldimg = jQuery('.MagicZoom').children('img');
    var tempimg = jQuery('<img class="tempimg" />'); 
    tempimg.attr('src',newimgsrc);
    jQuery('.MagicZoom').append(tempimg);

    jQuery('.MagicZoom').children('img').not('.tempimg').stop(true,true).fadeTo(400,0,function()
    {
      jQuery('.MagicZoom').children('img').not('.tempimg').attr('src',newimgsrc);
      if (typeof changetimer == 'object')
        clearTimeout(changetimer);
      changetimer = setTimeout(function()
      {
        jQuery('.MagicZoom').children('img').not('.tempimg').stop(true,true).fadeTo(0,1);
        jQuery('.MagicZoom').find('.MagicZoomBigImageCont img').attr('src',newimgsrcfull);
        tempimg.remove();
        currswapping = false;
      },200);
    });
  }
  
  jQuery(document).ready(function($) {
    curranimating = false;
    currswapping = false;
    var $magictoolbox = jQuery('.MagicToolboxContainer');
    if ($magictoolbox.length == 0) return;
    var $containeritems = $magictoolbox.find('.MagicToolboxSelectorsContainer a');
    
    // mark last pagination, first as selected
    $containeritems.first().addClass('selected');
    $containeritems.last().addClass('last');

    // new width of pagination containers
    if (jQuery.browser.msie && parseInt(jQuery.browser.version,10) == 9)
      $containeritems.parent('div').css('width',($containeritems.first().outerWidth()+parseInt($containeritems.first().css('marginRight'),10))*$containeritems.length + 'px');
    else
      $containeritems.parent('div').css('width',($containeritems.first().outerWidth()+parseInt($containeritems.first().css('marginRight'),10))*$containeritems.length - parseInt($containeritems.first().css('marginRight'),10) + 'px');

    // add selected state to pagination
    $containeritems.click(function()
    {
      if (curranimating) return;
      curranimating = true;
      $(this).siblings('a').removeClass('selected');
      $(this).addClass('selected');
      setTimeout(function() { curranimating = false; },400);
    });

    // set interaction with pager arrows
    $magictoolbox.siblings('.pager').click(function()
    {
      if (curranimating) return;
      if ($(this).hasClass('previous'))
      {
        if ($containeritems.filter('.selected').prev('a').length > 0)
        {
          // $containeritems.filter('.selected').prev().trigger('click');
          simulatedSwap($containeritems.filter('.selected').prev());
        }
        else
          // $containeritems.last().trigger('click');
          simulatedSwap($containeritems.last());
      }
      else
      {
        if ($containeritems.filter('.selected').next('a').length > 0)
          // $containeritems.filter('.selected').next().trigger('click');
          simulatedSwap($containeritems.filter('.selected').next());
        else
          // $containeritems.first().trigger('click');
          simulatedSwap($containeritems.first());
      }
    });

    // set simplified version of magic zoom for IE8, IE7
    if (jQuery.browser.msie && parseInt(jQuery.browser.version,10) < 9)
    {
      // MagicZoom.options = { 'zoom-fade': false, 'hint': false, 'opacity': 100, 'selectors-effect': false, 'smoothing': false, 'show-loading': false };
      jQuery('.MagicToolboxContainer .MagicZoom').hover(
        function()
        {
          if (typeof infotimer != 'undefined')
            clearTimeout(infotimer);
          jQuery('.information .left,.information .right').hide();
        },
        function()
        {
          infotimer = setTimeout(function() { jQuery('.information .left,.information .right').show(); },400);  
        }
      );
    }
  });
})();

/*
 * Catalog Sizing Chart
 */

(function() {
  
  jQuery(document).ready(function($) {

    var $overlay = jQuery('.tempoverlay');
    if ($overlay.length == 0) return;

    var overlayscreen = jQuery('<div id="sizeoverlay_screen">');
    var overlaycontainer = jQuery('<div id="sizeoverlay_overlaycontainer"><div class="floater"></div></div>');
    jQuery('body').append(overlayscreen);
    jQuery('body').append(overlaycontainer);

    if ($overlay.parent().attr('id') != 'sizeoverlay_overlaycontainer')
      jQuery(overlaycontainer).append($overlay);

    jQuery('#sizechart_link').click(function()
    {
      if (parseInt(jQuery('#sizeoverlay_screen').css('opacity'),10) == 0)
      {
        // set overlay position
        jQuery('#sizeoverlay_screen,#sizeoverlay_overlaycontainer').css('height',jQuery('body').height() + 'px');
        jQuery('#sizeoverlay_overlaycontainer .floater').css('height',jQuery(window).scrollTop() + (jQuery(window).height()/2) + 'px');

        $overlay.find('.chart').not(':eq(0)').css('opacity','0').css('display','none');

        // auto select proper tab
        var category = jQuery.trim(jQuery('#sizechart_link').attr('class')).toLowerCase();
        if (category != "")
        var tabs = $overlay.find('.tabs li');
       /* for (var i = 0; i < tabs.length; i++)
        {
          if ((jQuery.trim(tabs.eq(i).text()).toLowerCase() == "mens" && category == "men") ||
              (jQuery.trim(tabs.eq(i).text()).toLowerCase() == "womens" && category == "women") ||
              (jQuery.trim(tabs.eq(i).text()).toLowerCase() == "kids" && (category == "boys" || category == "girls")) ||
              (jQuery.trim(tabs.eq(i).text()).toLowerCase() == "baby" && category == "baby"))
               {
            tabs.eq(i).trigger('click');
            break;
          }
        }*/

        if (jQuery.browser.msie && parseInt(jQuery.browser.version,10) < 9)
        {
          // jQuery('#inspiredby_overlay').css('top',(jQuery(window).height()/2)- + 'px');
          jQuery('#sizeoverlay_screen').fadeTo(800,0.5);
          jQuery('#sizeoverlay_overlaycontainer').fadeIn(800);
        }
        else {
          jQuery('#sizeoverlay_screen,#sizeoverlay_overlaycontainer').fadeIn(800);
          // // center charts
          // var charts = $overlay.find('.chart');
          // for (var i = 0; i < charts.length; i++)
          // {
          //   charts.eq(i).css('marginLeft',($overlay.width() - charts.eq(i).width())/2 + 'px');
          // }
        }
      }
    });

    // overlay tab click functionality
    var $tabs = $overlay.find('.tabs li');
    $tabs.click(function()
    {
      if (jQuery(this).hasClass('selected') || $overlay.find('.active').length == 0) return;
      $overlay.find('.selected').removeClass('selected');
      jQuery(this).addClass('selected');
      var newchart = jQuery.trim(jQuery(this).text()).toLowerCase();
      $overlay.find('.chart.active').fadeTo(500,0,function() 
      { 
        jQuery(this).css('display','none').removeClass('active');
        $overlay.find('.chart').filter('.' + newchart).css('display','inline-block').fadeTo(500,1,function() { jQuery(this).addClass('active'); });
      });
    });

    // overlay close functionality
    $overlay.find('.close').add('#sizeoverlay_overlaycontainer').click(function(e)
    {
        if (!jQuery(e.target).hasClass('close') && !(jQuery(e.target).attr('id') == 'sizeoverlay_overlaycontainer')) return;
        jQuery('#sizeoverlay_overlaycontainer,#sizeoverlay_screen').fadeOut(800);
    });

  });
  
 })();  


/*
 * Collection Slideshow
 */ 

 (function() {
  
  jQuery(document).ready(function($) {
    var $collectioncontainer = $('.collectionimg_container'),
    	$collectionimages = $('.collectionimg_container .images'),
		$previous = $collectioncontainer.find('.prev'),
		$next = $collectioncontainer.find('.next');
		
    if ($collectionimages.length == 0) return;

    // init
    // $collectionimages.first().fadeIn(1500,function() { $(this).addClass('active'); });
    
	/********** OLD VERSION - Commented Out by Gregory **********
	$collectionimages.first().addClass('active');

    $collectioncontainer.find('.arrow').click(function(){
      var active = $(this).siblings('.active');
      var nextactive;
      // find next slide
      if ($(this).hasClass('prev'))
      {
        if (active.prev('img').length == 0)
          nextactive = $collectionimages.last();
        else
          nextactive = active.prev('img');
      }
      else
      {
        if (active.next('img').length == 0)
          nextactive = $collectionimages.first();
        else
          nextactive = active.next('img');
      }
      nextactive.css('zIndex','2').css('display','block');
      active.fadeOut(600,function() { active.removeClass('active'); nextactive.css('zIndex','3').addClass('active'); });
    });
	********** End of OLD VERSION **********/
	
	$collectionimages.cycle({
      'fx': 'fade',
      'prev': $previous,
      'next': $next,
      'easing': 'easeOutQuint',
	  'pause' : true,
	  'speed' : 1200,
      'timeout': 3000
    });
	
	/***** Fade Prev/Next Arrows In and Out *****/
	jQuery('.collectionimg_container .arrow').css({'display':'none'});
	jQuery('.collectionimg_container').hover(function(){
		jQuery('.arrow', this).stop(true,true).fadeIn(200);
	}, function(){
		jQuery('.arrow', this).stop(true,true).fadeOut(200);
	});
	
  });
 })();


/*
 * Product Slideshow
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    var $product_view = $('.product-view');
    var $product_view_images = $product_view.find('.images');
    var $product_view_slideshow = $product_view_images.find('.inside');
    
    // incase there's only one image.
    if( $product_view_slideshow.find('img').length <= 1 ) {
	  $product_view_slideshow.addClass('single');
      return false;
    }
    
    $product_view_images.prepend('<div class="previous"><a href="#">&laquo;</a></div>');
    $product_view_images.append('<div class="next"><a href="#">&raquo;</a></div>');
    $product_view_images.append('<div class="clear"></div><div class="pager"></div>');
    
    var $previous = $product_view_images.find('.previous');
    var $next = $product_view_images.find('.next');
    var $pager = $product_view_images.find('.pager');
    
    $product_view_slideshow.cycle({
      'fx': 'fade',
      'prev': $previous,
      'next': $next,
      
      'pager': $pager,
      'pagerAnchorBuilder': function(idx, slide) {
        return '<a href="#">&bull;</a>';
      }

    });
    
  });
  
  jQuery(document).ready(function($) {
    
    var $product_view = $('.catalog-product-view .product-view');
    var min_height = 444;
    
    if( $product_view.height() < min_height ) {
      
      $product_view.css({
        'marginTop': ( ( min_height - $product_view.height() ) / 2 ),
        'marginBottom': ( ( min_height - $product_view.height() ) / 2 )
      })
      
    }
    
  });
  
})();


/*
 * Homepage Banner
 */
(function() {

  jQuery('.homepage-slideshow').cycle({
    'fx': 'fade'
  })

})();

/*
 * Homepage New Arrivals Carousel
 */
(function() {

  jQuery(document).ready(function($) {
    
    var $new_arrivals = $('.new-arrivals');
    var $inside = $new_arrivals.find('.inside');
    var $slides = $new_arrivals.find('.slides')

    // Should we not even put in any effort?
    if( $slides.find('.slide-group').length <= 1 ) {
      return false;
    }
    
    $inside.prepend('<div class="previous"><a href="#"><img src="/skin/frontend/default/etiquette/images/hp-prev-btn.png"></a></div>');
    $inside.append('<div class="next"><a href="#"><img src="/skin/frontend/default/etiquette/images/hp-next-btn.png"></a></div>');
    
    var $previous = $inside.find('.previous');
    var $next = $inside.find('.next');
    
    $slides.cycle({
      'fx': 'scrollHorz',
      'prev': $previous,
      'next': $next,
      'easing': 'easeOutQuint',
      'timeout': 0
    });
    
  });
  

})();

/*
 * Rich Cart Sliding Down
 */
(function() {

  jQuery(document).ready(function($) {
    
    var $cart = $('.rich-cart-container');
    $cart.hide();
    
    $cart.bind('drop', function() {
      if( $cart.is(':visible') ) {
        return false;
      }
      
      $cart.css({
        'position': 'fixed',
        'top': (-1*$cart.height()) + 'px',
        'left': '0px'
      }).show().animate({
        'top': 0
      }, 'normal', 'easeOutQuint');

      setTimeout(function() {
        $cart.trigger('rise');
      }, (10 * 1000));
      
    }).bind('rise', function() {
      
      $cart.animate({
        'top': (-1*$cart.height()) + 'px'
      }, 'normal', 'easeOutQuint', function() {
        $(this).hide();
      });
      
    });
    
    $cart.find('.close').bind('click', function(e) {
      e.preventDefault();
      
      $cart.trigger('rise');
    });
    
    // bind to elements    
    $('.header .quick-access li a.top-link-cart').bind('click', function(e) {
      e.preventDefault();
      
      $cart.trigger('drop');
    });
    
  });
  

})();



/*
 * Catalog Swatch Code
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    var $swatch_container = $('.swatch-listing');
    var $swatches = $swatch_container.find('.swatch');
    var num_swatches = $swatches.length;
    var per_row = 6;
    
    for( var _a = 0; _a < Math.ceil(num_swatches/per_row); _a++ ) {
      $swatch_container.append(
        '<div class="swatch-row clearfix" id="swatch-row-' + _a + '"></div>'
      );
    }
    
    $swatches.each(function(n) {
      var $this = $(this);
      
      $this.appendTo(
        $swatch_container.find('.swatch-row').eq( Math.floor( n / per_row ) )
      );
      
      $this.find('a.swatch-link').bind('click', function(e) {
        e.preventDefault();
        
        var $swatch_link = $(this);
        var $products = $this.find('.products');
        var $products_container = $('.products-container');
        
        var show = function() {          
          $products.height( $products.height() );

          $products = $products.clone().height(0).insertAfter( $this.parents('.swatch-row').eq(0) );
          
          var height = ( Math.ceil( $products.find('.product').length / 4 ) * 330 ) + 45;
          
          $products
              .wrap('<div class="products-container clearfix"></div>')
              .animate({
                'height': height
              })
              .find('a.close')
                .bind('click', function(e) {
                  e.preventDefault();
                  
                  $swatch_link.trigger('click');
                });
                
          var top = $products.offset().top - ( $(window).height() - height ) / 2;
          var top_limit = $swatch_link.offset().top - 10;
          
          $('html,body').animate({
            'scrollTop': ( top > top_limit ? top_limit : top )
          });
        };

        var hide = function( after ) {

          $products_container
            .stop(true,true)
            .animate({
              'height': 0
            }, 'normal', function() {
              $(this).remove();
              if( after ) after();
            });

        };
        
        // if it's already active
        if( $(this).hasClass('active') ) {
          $(this).removeClass('active');
          hide();
          
          return false;
        }
        
        // if it isn't active
        $('a.swatch-link.active').removeClass('active');
        $(this).addClass('active');

        if( $products_container.length ) {
          hide( show );
        } else {
          show();
        }
        
      });
    });
    
  });
  
  
})();



/*
 * Nicer Loading
 */
(function() {
  
  return false;
  
  // Homepage
  var selectors = ['.homepage-slideshow','.new-arrivals .slide','.featured-tiles a'];
  jQuery(document).ready(function($) {
    $(selectors).each(function(n) {
      $( selectors[n] ).fadeTo(0,0)
    })
  });
  
  jQuery(window).load(function($) {
    $(selectors).each(function(n) {
      $( selectors[n] ).delay(n*100).fadeTo(500,1)
    })
  });
  
})();

(function() {
  
  var selectors = ['.product-listing .product','.sub-category-list ul li','.swatch-listing .swatch'];
  jQuery(document).ready(function($) {
    $(selectors).each(function(n) {
      if (!jQuery.browser.msie || parseInt(jQuery.browser.version,10) >= 9)
        $( selectors[n] ).fadeTo(0,0);
    })
  });
  
  jQuery(window).load(function() {
    jQuery(selectors).each(function(n) {
      jQuery( selectors[n] ).each(function(a) {
        
        var $this = jQuery(this);
        setTimeout(function() {
          if (!jQuery.browser.msie || parseInt(jQuery.browser.version,10) >= 9)
            $this.fadeTo(500,1);
        }, a*10);
        
      })
    })
  });
  
})();


/*
 * Header Search
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    var $search = $('.search-bar');
    
    var $search_field = $('.header-container .header .quick-access .search-field');
    var search_field_width = $search_field.width();
    $search_field.width(0).hide();
    
    var $container = $('.header-container .header .quick-access p.text');
    var container_width = $container.width();
    
    var isOpen = false;
    var offsetWidth = 10;
    
    var open = function() {
      $container.width($container.width()).animate({
        'width': container_width + search_field_width + offsetWidth
      });

      $search_field.show().animate({
        'width': search_field_width + offsetWidth
      }, {
        step: function() {
          
        },
        complete: function() {
          $search_field.find('input').focus();
        }
      });
    };
    
    var close = function() {
      if( $search_field.find('input').val() != '' ) { return false; }
      
      $container.width($container.width()).animate({
        'width': container_width + offsetWidth
      });
      
      $search_field.animate({
        'width': '0px'
      });
      return true;
    };
    
    $('.search-bar').click(function(e) {
      
      if( e.target ) {
        if( $(e.target).is('input') ) {
          if( $(e.target).is('#search') ) {
            return false;
          }
        }
      }
      
      if( !isOpen ) {
        open();
        isOpen = true;
        return false;
      } else {
          if( close() )
              isOpen = false;
      }
      
    });
    
    $search.bind('focusout', function() {
          if( close() )
              isOpen = false;
    });
    
    return false;
    
    $search.click(function() {

      $search_field.width(1).animate({
        'width': '100px'
      }, {
        complete: function() {
          $search_field.show();
        }
      });
      
    }, function() {

      $search_field.animate({
        'width': '0px'
      }, {
        complete: function() {
          $search_field.hide();
        }
      });
      
    })
    
  });
  
})();



/*
 * Footer Search
 */
(function() {
  
  jQuery(document).ready(function($) {
    
    var $search = $('.search-bar-bot');
    
    var $search_field = $('.footer-container .main-nav li.section .search-field');
    var search_field_width = $search_field.width();
    $search_field.width(0).hide();
    
    var $container = $('.footer-container .main-nav li.section p.text');
    var container_width = $container.width();
    
    var isOpenbot = false;
    var offsetWidth = 10;
    
    var openBot = function() {
      $container.width($container.width()).animate({
        'width': container_width + search_field_width + offsetWidth
      });

      $search_field.show().animate({
        'width': search_field_width + offsetWidth
      }, {
        step: function() {
          
        },
        complete: function() {
          $search_field.find('input').focus();
        }
      });
    };
    
    var closeBot = function() {
      if( $search_field.find('input').val() != '' ) { return false; }
      
      $container.width($container.width()).animate({
        'width': container_width + offsetWidth
      });
      
      $search_field.animate({
        'width': '0px'
      });
      return true;
    };
    
    $('.search-bar-bot').click(function(e) {
      
      if( e.target ) {
        if( $(e.target).is('input') ) {
          if( $(e.target).is('#search') ) {
            return false;
          }
          if( $(e.target).is('#submitbtnbot') && isOpenbot ) {
              document.getElementById( "search_mini_form_bot" ).submit();
              return false; 
          }
        }
      }
      
      if( !isOpenbot ) {
        openBot();
        isOpenbot = true;
        return false;
      } else {
          if( closeBot() )
          {
              isOpenbot = false;
          }
      }
      
    });
    
    $search.bind('focusout', function() {
                     if( closeBot() )
                     {
                         isOpenbot = false;
                     }
    });
    
    return false;
    
    $search.click(function() {
      $search_field.show();
      $search_field.width(1).animate({
        'width': '100px'
      }, {
        complete: function() {
        }
      });
      
    }, function() {
      $search_field.animate({
        'width': '0px'
      }, {
        complete: function() {
          $search_field.hide();
        }
      });
      
    })
    
  });
  
})();




/*
 * Position Fixed with Scrolling
 */
(function() {
  
  var cTop;
  
  jQuery(document).ready(function($) {
    
    if( !$('.catalog-category-view li.active ul.level0').length ) {
	  console.log("this is true!");
      return false;
    }
    
    cTop = $('.catalog-category-view li.active ul.level0').offset().top - 5; // -5 for the border
    
    $(window).scroll(function() {
      var wTop = $(window).scrollTop();

      if( wTop >= cTop ) {      
        $('.main-container').addClass('fixedElems');
      } else {
        $('.main-container').removeClass('fixedElems');
      }
    });

  });

  return false; // exit() technically!

  setInterval(function() { jQuery('html,body').scrollTop(cTop-1); }, 1000);
  setTimeout(function() { setInterval(function() { jQuery('html,body').scrollTop(cTop); }, 1000); }, 500);

})();



/*
 * Increase/Decrease Qty buttons
 */
(function() {
	jQuery(document).ready(function() {
        jQuery('.qty .increment').click(function() {
			var $button = jQuery(this);
			var oldValue = $button.parent().find('input').val();
		
			if ($button.text() == '+') {
			  if(oldValue >= 1){
			  var newVal = parseFloat(oldValue) + 1;
			  } else {
				  newVal = 1;
			  }
			} else {
			  if (oldValue > 1) {
				  var newVal = parseFloat(oldValue) - 1;
			  } else {
				  newVal = 1;
			  }
			}
			$button.parent().find('input').val(newVal);
		});
    });
})();


/*
 * Contact Pg Accordion
 */
(function() {
	
	jQuery(document).ready(function($){
	
	   $('.cms-contact .accordion_container').accordion( {
		   	header: '.trg',
			active: '.open',
			alwaysOpen: false
	   });
	  
	});

})();


/*
 * Terms Pg Accordion
 */
(function() {
	
	jQuery(document).ready(function($){
	
	   $('.cms-customer-service .accordion_container').accordion({
		   	header: '.trg',
			autoHeight: false,
			active: '.open',
			alwaysOpen: false
	   });
	  
	});

})();


/*
 * Stockists Pg Accordion
 */
(function() {
	
	jQuery(document).ready(function($){
	
	   $('.cms-stockists .accordion_left, .cms-stockists .accordion_right').accordion({
		   	header: '.trg',
			autoHeight: false,
			active: '.open',
			alwaysOpen: false
	   });
	  
	});

})();
