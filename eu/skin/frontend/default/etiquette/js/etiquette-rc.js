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
    
    $('.cms-catalog .product-listing .product').each(function() {
      
      var $elems = $(this).find('.tooltip,img.alt');
      var timeout = null;
      
      $(this).hover(function() {
        var $this = $(this);
        
        timeout = setTimeout(function() {
          $elems.stop(true,true).fadeTo( time, 1 );
        }, delay);
        
      }, function() {
        clearTimeout( timeout );
        $elems.stop(true,true).fadeTo( time, 0 );
      });
      
      
      $elems.fadeTo(0,0);
    
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
      return false;
    }
    
    $product_view_images.prepend('<div class="previous"><a href="#"><img src="images/product/previous.png"></a></div>');
    $product_view_images.append('<div class="next"><a href="#"><img src="images/product/next.png"></a></div>');
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
    
    $inside.prepend('<div class="previous"><a href="#"><img src="images/product/previous.png"></a></div>');
    $inside.append('<div class="next"><a href="#"><img src="images/product/next.png"></a></div>');
    
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
    $('.product a,.header .quick-access .shopping-bag a').bind('click', function(e) {
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
      $( selectors[n] ).fadeTo(0,0)
    })
  });
  
  jQuery(window).load(function() {
    jQuery(selectors).each(function(n) {
      jQuery( selectors[n] ).each(function(a) {
        
        var $this = $(this);
        setTimeout(function() {
          $this.fadeTo(500,1);
        }, a*10);
        
      })
    })
  });
  
})();


/*
 * Search
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
        close();
        isOpen = false;
      }
      
    });
    
    $search.bind('focusout', function() {
      close();
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
 * Position Fixed with Scrolling
 */
(function() {
  
  var cTop;
  
  jQuery(document).ready(function($) {
    
    if( !$('.sub-category-list').length ) {
      return false;
    }
    
    cTop = $('.sub-category-list').offset().top - 5; // -5 for the border
    
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