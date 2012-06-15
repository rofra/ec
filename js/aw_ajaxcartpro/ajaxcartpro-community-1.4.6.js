/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

window.intPrevious = setInterval(function(){
	if(typeof AW_ACP != 'undefined' && document.body){
		if(typeof aw_cartDivClass == 'undefined'){
			 aw_cartDivClass =
				AW_ACP.theme == 'blank' ?
					'.block-cart' :
					'.mini-cart';

			if(!$$(aw_cartDivClass).length || !$$(aw_cartDivClass)[0].tagName){
				 aw_cartDivClass =  '.block-cart'
			}
		}
//        alert( aw_cartDivClass );
		if(typeof aw_topLinkCartClass == 'undefined'){
			 aw_topLinkCartClass = '.top-link-cart';
		}
		if(typeof aw_addToCartButtonClass == 'undefined'){
			 aw_addToCartButtonClass = '.form-button';
		}
		if(typeof aw_bigCartClass == 'undefined'){
            if (typeof($$('.layout-1column')[0]) != 'undefined')
                aw_bigCartClass = '.layout-1column';
            else if (typeof($$('.col-main')[0]) != 'undefined')
				aw_bigCartClass = '.col-main';
            else
                aw_bigCartClass = '.cart';
		}
        if(typeof aw_wishlistClass == 'undefined'){
			 if (typeof($$('.my-wishlist')[0]) != 'undefined')
                aw_wishlistClass = '.my-wishlist';
             else
                aw_wishlistClass = '.padder';
		}
        aw_cartDivClass = ".rich-cart-container";

        if(typeof aw_topWishlistLinkCartClass == 'undefined'){
            aw_topWishlistLinkCartClass = '.top-link-wishlist';
        }

		if (window.location.toString().search('/product_compare/') != -1){
			win = window.opener;
		}
		else{
			win = window;
		}
		clearInterval(intPrevious)
	}
}, 500);

function ajaxcartprodelete(url){
	showProgressAnimation();
	url = getCommonUrl(url)


    new Ajax.Request(url, {
          onSuccess: function(resp){
				try{
					if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
				}catch(e){

					return;
				}
				hideProgressAnimation();
                __onACPRender()
                updateCartView(resp, '');
                loc = document.location.href.indexOf( "/us/" ) > 0 ?"http://www.etiquetteclothiers.com/us/empty":"http://www.etiquetteclothiers.com/eu/empty";
                parent.document.getElementById( "mycframe" ).src = loc;
			}
        });


}

function updateCartView(resp){
    if (AW_ACP.isCartPage) return updateBigCartView(resp);

	var __cartObj = $$(aw_cartDivClass)[0];
//    alert( __cartObj );
	if(__cartObj)
    {
        if (typeof(__cartObj.length) == 'number') __cartObj = __cartObj[0];
            // custom rachel
        __cartObj.innerHTML = resp.cart;
        fixRichCart();
        updateDeleteLinks();
        updateTopLinks(resp);
	loc = document.location.href.indexOf( "/us/" ) > 0 ?"http://www.etiquetteclothiers.com/us/empty":"http://www.etiquetteclothiers.com/eu/empty";
	parent.document.getElementById( "mycframe" ).src = loc;
        return;
            // end custom rachel
        var oldHeight = __cartObj.offsetHeight;

        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.cart;
        $(tmpDiv).cleanWhitespace();

        var tmpParent = __cartObj.parentNode;
        tmpParent.replaceChild($(tmpDiv).select(aw_cartDivClass)[0], __cartObj);

        /* Details popup support */

        var __cartObj = $$(aw_cartDivClass)[0];
        var newHeight = __cartObj.offsetHeight;

        addEffectACP(__cartObj, aw_ajaxcartpro_cartanim);
        truncateOptions();
    }
    updateDeleteLinks();
	updateTopLinks(resp);
}

function updateWishlist(resp)
{
	var wishlistObj = $$(aw_wishlistClass)[0];

    if(wishlistObj){
        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.wishlist;

        var tmpParent = wishlistObj.parentNode;
        tmpParent.replaceChild(tmpDiv.firstChild, wishlistObj);
    }
}

