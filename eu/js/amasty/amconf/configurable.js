// extension Code
function applyProductZoom()
{
    if ($('image'))
    {
        new Product.Zoom('image', 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');
    }
}

AmConfigurableData = Class.create();
AmConfigurableData.prototype = 
{
    textNotAvailable : "",
    
    mediaUrlMain : "",
    
    currentIsMain : "",
    
    optionProducts : null,
    
    optionDefault : new Array(),
    
    initialize : function(optionProducts)
    {
        this.optionProducts = optionProducts;
    },
    
    hasKey : function(key)
    {
        return ('undefined' != typeof(this.optionProducts[key]));
    },
    
    getData : function(key, param)
    {
        if (this.hasKey(key) && 'undefined' != typeof(this.optionProducts[key][param]))
        {
            return this.optionProducts[key][param];
        }
        return false;
    },
    
    saveDefault : function(param, data)
    {
        this.optionDefault['set'] = true;
        this.optionDefault[param] = data;
    },
    
    getDefault : function(param)
    {
        if ('undefined' != typeof(this.optionDefault[param]))
        {
            return this.optionDefault[param];
        }
        return false;
    }
}
// extension Code End

Product.Config.prototype.fillSelect = function(element){
    var attributeId = element.id.replace(/[a-z]*/, '');
    var options = this.getAttributeOptions(attributeId);
    this.clearSelect(element);
    element.options[0] = new Option(this.config.chooseText, '');

    var prevConfig = false;
    if(element.prevSetting){
        prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
    }

    if(options) {
        
        // extension Code
        if (this.config.attributes[attributeId].use_image)
        {
            if ($('amconf-images-' + attributeId))
            {
                $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
            }
            
            holder = element.parentNode;
            holderDiv = document.createElement('div');
            holderDiv = $(holderDiv); // fix for IE
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId;
            holder.insertBefore(holderDiv, element);
        }
        // extension Code End
        
        
        
        var index = 1;
        for(var i=0;i<options.length;i++){
            var allowedProducts = [];
            if(prevConfig) {
                for(var j=0;j<options[i].products.length;j++){
                    if(prevConfig.config.allowedProducts
                        && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                        allowedProducts.push(options[i].products[j]);
                    }
                }
            } else {
                allowedProducts = options[i].products.clone();
            }

            if(allowedProducts.size()>0)
            {
                // extension Code
                if (this.config.attributes[attributeId].use_image)
                {
                    image = document.createElement('img');
                    image = $(image); // fix for IE
                    image.id = 'amconf-image-' + options[i].id;
                    image.src   = options[i].image;
                    image.addClassName('amconf-image');
                    image.observe('click', this.configureImage.bind(this))
                    holderDiv.appendChild(image);
                }
                // extension Code End
                
                
                options[i].allowedProducts = allowedProducts;
                element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                element.options[index].config = options[i];
                index++;
            }
        }
    }
}

Product.Config.prototype.configureElement = function(element) 
{
    // extension Code
    optionId = element.value;
    if ($('amconf-image-' + optionId))
    {
        this.selectImage($('amconf-image-' + optionId));
    } else 
    {
        attributeId = element.id.replace(/[a-z-]*/, '');
        if ($('amconf-images-' + attributeId))
        {
        $('amconf-images-' + attributeId).childElements().each(function(child){
            child.removeClassName('amconf-image-selected');
        });
        }
    }
    // extension Code End
    
    this.reloadOptionLabels(element);
    if(element.value){
        this.state[element.config.id] = element.value;
        if(element.nextSetting){
            element.nextSetting.disabled = false;
            this.fillSelect(element.nextSetting);
            this.resetChildren(element.nextSetting);
        }
    }
    else {
        // extension Code
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                attributeId = element.childSettings[i].id.replace(/[a-z-]*/, '');
                if ($('amconf-images-' + attributeId))
                {
                    $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
                }
            }
        }
        // extension Code End
        
        this.resetChildren(element);
        
        // extension Code
        if (this.settings[0].hasClassName('no-display'))
        {
            this.processEmpty();
        }
        // extension Code End
    }
    this.reloadPrice();
    
    // extension Code
    var key = '';
    this.settings.each(function(select){
        key += select.value + ',';
    });
    key = key.substr(0, key.length - 1);
    this.updateData(key);
    
    // for compatibility with custom stock status extension:
    if ('undefined' != typeof(stStatus) && 'function' == typeof(stStatus.onConfigure))
    {
        stStatus.onConfigure(key, this.settings);
    }
    // extension Code End
}
    
// these are new methods introduced by the extension
// extension Code
Product.Config.prototype.configureImage = function(event){
    var element = Event.element(event);
    attributeId = element.parentNode.id.replace(/[a-z-]*/, '');
    optionId = element.id.replace(/[a-z-]*/, '');
    
    this.selectImage(element);
    
    $('attribute' + attributeId).value = optionId;
    this.configureElement($('attribute' + attributeId));
}

Product.Config.prototype.selectImage = function(element)
{
    attributeId = element.parentNode.id.replace(/[a-z-]*/, '');
    $('amconf-images-' + attributeId).childElements().each(function(child){
        child.removeClassName('amconf-image-selected');
    });
    element.addClassName('amconf-image-selected');
}

Product.Config.prototype.processEmpty = function()
{
    $$('.super-attribute-select').each(function(select) {
        if (select.disabled)
        {
            var attributeId = select.id.replace(/[a-z]*/, '');
            if ($('amconf-images-' + attributeId))
            {
                $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
            }
            holder = select.parentNode;
            holderDiv = document.createElement('div');
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId;
            holderDiv.innerHTML = confData.textNotAvailable;
            holder.insertBefore(holderDiv, select);
        }
    }.bind(this));
}

Product.Config.prototype.clearConfig = function()
{
    this.settings[0].value = "";
    this.configureElement(this.settings[0]);
    return false;
}

Product.Config.prototype.updateData = function(key)
{
    if ('undefined' == typeof(confData))
    {
        return false;
    }
    if (confData.hasKey(key))
    {
        // getting values of selected configuration
        if (confData.getData(key, 'short_description'))
        {
            $$('.short-description div').each(function(container){
                if (!confData.getDefault('short_description'))
                {
                    confData.saveDefault('short_description', container.innerHTML);
                }
                container.innerHTML = confData.getData(key, 'short_description');
            }.bind(this));
        }
        if (confData.getData(key, 'description'))
        {
            $$('.box-description div').each(function(container){
                if (!confData.getDefault('description'))
                {
                    confData.saveDefault('description', container.innerHTML);
                }
                container.innerHTML = confData.getData(key, 'description');
            }.bind(this));
        }
        if (confData.getData(key, 'media_url'))
        {
            // should reload images
            new Ajax.Request(confData.getData(key, 'media_url'), {
                method: 'post',
                evalJS: false,
                onSuccess: function(transport) {
                    $$('.product-img-box').each(function(container){
                        if (!confData.getDefault('media'))
                        {
                            confData.saveDefault('media', container.innerHTML);
                        }
                        container.innerHTML = transport.responseText;
                    }.bind(this));
                    var tm = self.setTimeout("applyProductZoom()",2500);
                    confData.currentIsMain = false;
                }
            });
        }
    } else 
    {
        // setting values of default product
        if (true == confData.getDefault('set'))
        {
            if (confData.getDefault('short_description'))
            {
                $$('.short-description div').each(function(container){
                    container.innerHTML = confData.getDefault('short_description');
                }.bind(this));
            }
            if (confData.getDefault('description'))
            {
                $$('.box-description div').each(function(container){
                    container.innerHTML = confData.getDefault('description');
                }.bind(this));
            }
            if (confData.getDefault('media') && !confData.currentIsMain)
            {
                new Ajax.Request(confData.mediaUrlMain, {
                    method: 'post',
                    evalJS: false,
                    onSuccess: function(transport) {
                        $$('.product-img-box').each(function(container){
                            confData.saveDefault('media', container.innerHTML);
                            container.innerHTML = transport.responseText;
                        }.bind(this));
                        var tm = self.setTimeout("applyProductZoom()",2500);
                        confData.currentIsMain = true;
                    }
                });
                /*$$('.product-img-box').each(function(container){
                    imageboxHtml = confData.getDefault('media').replace(/style(.+)?id=\"image/i, "id=\"image");
                    container.innerHTML = imageboxHtml;
                }.bind(this));
                var tm = self.setTimeout("applyProductZoom()",2500);*/
            }
        }
    }
}
// extension Code End
