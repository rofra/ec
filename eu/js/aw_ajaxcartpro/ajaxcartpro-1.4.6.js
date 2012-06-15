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
 

Prototype.Browser.IE6 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6;
Prototype.Browser.IE7 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7;
Prototype.Browser.IE8 = Prototype.Browser.IE && !Prototype.Browser.IE6 && !Prototype.Browser.IE7;

window.ACPTop = 200;
 
if(!Prototype.Browser.IE6){

    setLocation = function(url){
        if(window.location.href.match('https://') && !url.match('https://')){
            url = url.replace('http://','https://')
        }
        if(AW_ACP.isCartPage && ((url.search('/add') != -1 ) || (url.search('/remove') != -1 )) ){
            ajaxcartsend(url+'awacp/1/is_checkout/1', 'url', '', '');
        }else if (url.search('checkout/cart/add') != -1){
            ajaxcartsend(url+'awacp/1', 'url', '', '');
        }else if (url.search('wishlist/index/cart') != -1){
            ajaxcartsendwishlist(url+'awwishl/1/awacp/1', 'url', '', '');
        }else if (url.search('options=cart') != -1){
            ajaxcartsendconfigurable(url);
        }
        else
        {
            window.location.href = url;
        }
    }
}

function addSubmitEvent()
{
    if (typeof productAddToCartForm != 'undefined')
    {
        productAddToCartForm.submit = function(url){
            if(this.validator && this.validator.validate()){
                ajaxcartsend('?awacp=1', 'form', this, '');
            }
            return false;
        }

        productAddToCartForm.form.onsubmit = function() {
            productAddToCartForm.submit();
            return false;
        };
    }

    
    
}

function addAcpSubmitEvent()
{
    if (typeof productAddToCartFormAcp != 'undefined')
    {
        productAddToCartFormAcp.submit = function(url){
            if(this.validator && this.validator.validate()){
                if (AW_ACP.isCartPage)
                    ajaxcartsend('?awacp=1&is_checkout=1', 'form', this, '');
                else
                    ajaxcartsend('?awacp=1', 'form', this, '');
            }
            return false;
        }

        productAddToCartFormAcp.form.onsubmit = function() {
            productAddToCartFormAcp.submit();
            return false;
        };
    }
}


if(!Prototype.Browser.IE6){

    var cnt1 = 20;
	__intId = setInterval(
		/* Hangs event listener for @ADD TO CART@ links*/

		function(){
			cnt1--;
			if(typeof productAddToCartForm != 'undefined'){
				try {
					// This fix is applied to magento <1.3.1
                    $$('#product_addtocart_form '+aw_addToCartButtonClass).each(function(el){
                        el.setAttribute('type', 'button')
                    })
				}catch(err){
					
				}
				
                if (AW_ACP.hasFileOption == false) addSubmitEvent();
                
				clearInterval(__intId);
			}
			if(!cnt1) clearInterval(__intId);
		},
		500
	);



	var cnt2 = 20;
	__intId2 = setInterval(
		/* This hangs event listener on @DELETE@ items from cart*/
		function(){	
			cnt2--;
			if(typeof aw_cartDivClass!= 'undefined' && $$(aw_cartDivClass).length || ((typeof AW_ACP !== 'undefined') && AW_ACP.isCartPage)){
                updateDeleteLinks();
				clearInterval(__intId2);
			}
			if(!cnt2) clearInterval(__intId);
		},
		500
	);
}





function setPLocation(url, setFocus){
    if (url.search('checkout/cart/add') != -1){ //CART ADD
        window.opener.focus();

        if (url[url.length-1] == '/') delim = '';
        else delim = '/';

        if (window.opener.location.pathname.search('checkout/cart') == -1)
            window.opener.ajaxcartsend(url+delim+'awacp/1', 'url', '');
        else
            window.opener.ajaxcartsend(url+delim+'awacp/1/is_checkout/1', 'url', '');
	}
	else{
		if(setFocus) {
			window.opener.focus();
		}
        window.opener.location.href = url;
	}
}

function ajaxcartsendwishlist(url, type, obj){
    url = getCommonUrl(url);
    showProgressAnimation();
    new Ajax.Request(url, {
          onSuccess: function(resp){
                try{
                    if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
				}catch(e){
					win.location.href=url;
					hideProgressAnimation();
					return;
				}
                hideProgressAnimation();
				if (resp.r != 'success'){
                    if (resp.redirect)
                        win.location.href = resp.redirect;
                    else
                        win.location.href=url;
                }
				else{
					if(AW_ACP.useConfirmation){	
						showConfirmDialog(resp.product_name);
					}
					__onACPRender();
					updateCartView(resp);
                    updateTopLinks(resp);
                    updateWishlist(resp);
                    updateWishlistTopLinks(resp);
                    updateAddLinks();
				}
			}
        });
}

function ajaxcartsend(url, type, obj){
    url = getCommonUrl(url)
	
	showProgressAnimation();
	if (type == 'form'){		
//		var aForm = $('product_addtocart_form_acp') ? $('product_addtocart_form_acp') : $('product_addtocart_form');
        $(obj.form.id).action += url;
//        alert( url );
        $(obj.form.id).request({
            onComplete:  function(resp){

                if (typeof(resp.responseText) == 'string'){
                    try{
                        eval('resp = ' + resp.responseText);
//                        alert( resp.responseText );
                    }catch(e){
                        if (obj.form.submit()){
                            return;
                        }
                        else{
                            win.location.href = obj.form.action;
                            return;
                        }
                    }
                }
				hideProgressAnimation();
				if (resp.r != 'success'){
					obj.form.submit();
				}
				else{
					__onACPRender();
                    if(AW_ACP.useConfirmation && (url.search('is_checkout/1') != 1)){
						showConfirmDialog(resp.product_name);
					}
					updateCartView(resp);
                    var $cart = jQuery('.rich-cart-container');
                    $cart.trigger('drop');
				}
			}
        })

	}
	if (type == 'url'){
		new Ajax.Request(url, {
          onSuccess: function(resp){
				try{
					if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
				}catch(e){
					win.location.href=url;
					hideProgressAnimation();
					return;
				}
				hideProgressAnimation();
                if (resp.r != 'success'){
					win.location.href=url;
				}
				else{		
                    if(AW_ACP.useConfirmation && (url.search('is_checkout/1') == -1)){
						showConfirmDialog(resp.product_name);
					}
					__onACPRender();
					updateCartView(resp);
                    var $cart = jQuery('.rich-cart-container');
                    $cart.trigger('drop');
				}
			}
        });

	}
}

function __onACPRender(){
    if(AW_ACP.onRender && AW_ACP.onRender.length){
	$A(AW_ACP.onRender).each(function(h){h(AW_ACP)})
    }
}

function addEffectACP(obj, effect)
{
    if (effect == 'opacity'){
        $(obj).hide();
        new Effect.Appear(obj);

	}
	if (effect == 'grow'){
        $(obj).hide();
        new Effect.BlindDown(obj);
	}
	if (effect == 'blink'){
        new Effect.Pulsate(obj);
	}
}


function updateDeleteLinks(){
	var tmpLinks = document.links;
	for (i=0; i<tmpLinks.length; i++){
		if (tmpLinks[i].href.search('checkout/cart/delete') != -1){
			url = tmpLinks[i].href.replace(/\/uenc\/.+,/g, "");
			var del = url.match(/delete\/id\/\d+\//g);
			var id = del[0].match(/\d+/g);
			if (window.location.protocol == 'https:'){
				aw_base_url = aw_base_url.replace("http:", "https:");
			}	
			if(!AW_ACP.isCartPage){
				tmpLinks[i].href = 'javascript:ajaxcartprodelete("' + aw_base_url + 'ajaxcartpro/cart/remove/id/' + id +'")';
			}else{
				tmpLinks[i].href = 'javascript:ajaxcartprodelete("' + aw_base_url + 'ajaxcartpro/cart/remove/id/' + id +'/is_checkout/1")';
			}
		}
	}
}

function updateTopLinks(resp){
    if($$(aw_topLinkCartClass).length){
        $$(aw_topLinkCartClass)[0].title = $$(aw_topLinkCartClass)[0].innerHTML = resp.links;
    }
}

function updateWishlistTopLinks(resp){
    if($$(aw_topWishlistLinkCartClass).length){
        $$(aw_topWishlistLinkCartClass)[0].innerHTML = resp.wishlist_links;
    }
}

window.updateBigCartView = function (resp){

    $$(aw_bigCartClass)[0].innerHTML = resp.cart
	if($('shopping-cart-table')){
		decorateTable('shopping-cart-table')
	}

    updateDeleteLinks();
	updateTopLinks(resp);
	updateAddLinks();
	
	var scripts = resp.cart.extractScripts();
    for (var i=0; i<scripts.length; i++)
    {
        if (typeof(scripts[i]) != 'undefined')
        {
            try
            {
                eval(scripts[i].replace(/var /gi, ""));
            } catch(e){ }
        }
    }
	
}

function showProgressAnimation(){
    alignBlock($$('.ajaxcartpro_progress')[0], 260, 50, 'progress');
}

function showConfirmDialog(product_name){
    if (product_name) $('acp_product_name').innerHTML = product_name;
    block = $$('.ajaxcartpro_confirm')[0];
    alignBlock(block, 260, 104, 'confirmation');
    block.style.display = 'block';
    if (typeof($$('.ajaxcartpro_confirm .focus')[0]) != 'undefined') $$('.ajaxcartpro_confirm .focus')[0].focus();

    var ACPcountdown = $('ACPcountdown');
    if(typeof ACPcountdown != 'undefined' && AW_ACP.counterBegin>0)
    {
        ACPcountdown.innerHTML = AW_ACP.counterBegin;
        if (typeof __intId3 != 'undefined') clearInterval(__intId3);
        __intId3 = setInterval(
            function(){
                if ( parseInt(ACPcountdown.innerHTML) ){
                    ACPcountdown.innerHTML = parseInt(ACPcountdown.innerHTML)-1;
                }
                else
                { 
                    clearInterval(__intId3);
                    block.style.display = "none";
                    ACPcountdown.innerHTML = AW_ACP.counterBegin;
                }

            },
            1000
        );
    }
}

function hideProgressAnimation(){

	$$('.ajaxcartpro_progress')[0].style.display = 'none';
}

if(!Prototype.Browser.IE6){
    document.observe("dom:loaded", function() {
		updateAddLinks()
		
		// Some other onclicks
		$('aw_acp_continue').onclick = function(e){
			e = e||event;
			if(e.preventDefault)
				e.preventDefault()
			$$('.ajaxcartpro_confirm')[0].style.display='none';return false;
		}
		
		$('aw_acp_checkout').onclick = function(e){
			$$('.ajaxcartpro_confirm')[0].style.display='none';return true;
		}	
		
		// Test for minicart
		
		if((typeof aw_cartDivClass != 'undefined') && ($$(aw_cartDivClass).length || ((typeof AW_ACP !== 'undefined') && AW_ACP.isCartPage))){
			updateDeleteLinks();
		}
		
	})
}

function updateAddLinks(){
	var ats = document.links;
	for (i=ats.length-1; i>=0; i--){
		if (ats[i].href.search('checkout/cart/add') != -1){
			ats[i].onclick = function(link){
				return function(){
					setLocation(link)
				}
			}(ats[i].href);
			ats[i].href="javascript:void(0)";
		}
        if (ats[i].href.search('wishlist/index/cart') != -1){
			ats[i].onclick = function(link){
				return function(){
					setLocation(link)
				}
			}(ats[i].href);
			ats[i].href="javascript:void(0)";
		}
	}
}

function getCommonUrl(url){
	if(window.location.href.match('www.') && url.match('http://') && !url.match('www.')){
		url = url.replace('http://', 'http://www.');
	}else if(!window.location.href.match('www.') && url.match('http://') && url.match('www.')){
		url = url.replace('www.', '');
	}
	return url;
}

var productAddToCartFormAcp;
function ajaxcartsendconfigurable(url)
{
    showProgressAnimation();
    urlToSend = url + '&ajaxcartpro=1';
    new Ajax.Request(urlToSend, {
          onSuccess: function(resp){
                if (resp.responseText == 'false')
                {
                    window.location = url;
                }
                else
                { 
                    tmpDiv = win.document.createElement('div');
                    var scripts = resp.responseText.extractScripts();

                    tmpDiv.innerHTML = resp.responseText.stripScripts();
                    win.document.body.appendChild(tmpDiv);
                    
                    showOptionsDialog();
                    hideProgressAnimation();

                    productAddToCartFormAcp = new VarienForm('product_addtocart_form_acp');
                    decorateGeneric($$('#product-options-wrapper dl'), ['last']);
                    addAcpSubmitEvent();
                    if (typeof($$('#acp_configurable_block .focus')[0]) != 'undefined') $$('#acp_configurable_block .focus')[0].focus();
                    
                    for (var i=0; i<scripts.length; i++)
                    {
                        if (typeof(scripts[i]) != 'undefined')
                        {
                            eval(scripts[i]);
                        }
                    }
                }
			}
        });
}

function showOptionsDialog()
{
    alignBlock($('acp_product_options'), 400, $('acp_product_options').offsetHeight, 'custom_options');
}

function alignBlock(block, width, height, blockType)
{
    if (blockType == 'confirmation' && !AW_ACP.useConfirmation)
        return false;

    if (blockType == 'progress' && !AW_ACP.useProgress)
        return false;

    block.style.display = 'block';
    block.style.width = width + 'px';
    block.style.height = height + 'px';
    block.style.left = document.viewport.getWidth()/2 - width/2 + 'px';
    
	if (Prototype.Browser.IE && !navigator.appVersion.match("8")){
		block.style.position = 'absolute';
		window.ACPTop = 200;
	}
	if (aw_ajaxcartpro_proganim == 'center'){
		if (!(Prototype.Browser.IE && !navigator.appVersion.match("8"))){
            block.style.top = (document.viewport.getHeight()/2 - height/2) + 'px';
		}else{
            window.ACPTop = 200;
        }
		}
	if (aw_ajaxcartpro_proganim == 'top'){
		if (!(Prototype.Browser.IE && !navigator.appVersion.match("8"))){
		    block.style.top = '0px';
		}else{
		     // IE7-
		    window.ACPTop = 0;
		}
	}
	if (aw_ajaxcartpro_proganim == 'bottom'){

		block.style.bottom = '0px';
	}
}

function validateDownloadableCallback(elmId, result)
{
    var container = $('downloadable-links-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

function validateOptionsCallback(elmId, result)
{
    var container = $(elmId).up('ul.options-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

function acpSubmit()
{
    if(productAddToCartFormAcp.validator&&productAddToCartFormAcp.validator.validate())
    {
        productAddToCartFormAcp.submit();
        $('acp_configurable_block').remove();
    }
}



// this is a copy of the above
//var counterajax = Array();
//var counterintid = Array();
function setupAJAXCartForForm( myform )
{
        //    alert( "hm" + myform );
        //    alert( myform.id );
    if(!Prototype.Browser.IE6){

        if(typeof myform != 'undefined'){
            try {
                $$('#' + myform.id +' '+aw_addToCartButtonClass).each(function(el){
                                                                          el.setAttribute('type', 'button')
                                                                              })
//                    alert( "hm found one" );
                    }catch(err){

            }

            myform.submit = function(url){
//                alert( this.validator );
//                alert( this.validator.validate() );
                if(this.validator && this.validator.validate()){
//                    alert( "sending" );
                    ajaxcartsend('?awacp=1', 'form', this, '');
                }
                return false;
            }
        }

    }
}



