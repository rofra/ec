var magicToolboxLinks = [];
var optionLabels = {};
var optionTitles = {};
var optionProductIDs = {};
//var magicToolboxOptionTitles = '';defined in header.phtml
var allowMagicToolboxChange = true;

function MagicToolboxPrepareOptions() {
    var pid = optionsPrice.productId;
    var container = document.getElementById('MagicToolboxSelectors'+pid);
    if(container) magicToolboxLinks = container.getElementsByTagName('a');
    //for configurable products
    if(typeof spConfig != 'undefined' && typeof spConfig.config.attributes != 'undefined') {
        for(var attributeID in spConfig.config.attributes) {
            optionLabels[attributeID] = {};
            optionProductIDs[attributeID] = {};
            optionTitles[attributeID] = spConfig.config.attributes[attributeID].label.toLowerCase();
            for(var optionID in spConfig.config.attributes[attributeID].options) {
                var option = spConfig.config.attributes[attributeID].options[optionID];
                if(typeof option == 'object') {
                    optionLabels[attributeID][option.id] = option.label.replace(/(^\s+)|(\s+$)/g, "")/*.replace(/"/g, "'")*/.toLowerCase();
                    optionProductIDs[attributeID][option.id] = {};
                    for(var i = 0, productsLength = option.products.length; i < productsLength; i++) {
                        optionProductIDs[attributeID][option.id][i] = option.products[i];
                    }
                }
            }
        }
    }
    //if(typeof opConfig != 'undefined') opConfig.reloadPrice();
}

function MagicToolboxClickElement(element, eventType, eventName) {
    var event;
    if (document.createEvent) {
        event = document.createEvent(eventType);
        event.initEvent(eventName, true, true);
        element.dispatchEvent(event);
    } else {
        event = document.createEventObject();
        event.eventType = eventType;
        element.fireEvent('on' + eventName, event);
    }
    return event;
}

function MagicToolboxChangeOption(element, optionTitle) {

    if(!allowMagicToolboxChange) {
        allowMagicToolboxChange = true;
        return;
    }

    if(MagicToolboxInArray(optionTitle, magicToolboxOptionTitles)) {
        var id = '';
        if(element.type == 'radio' && element.checked) {
            id = element.name.replace('options[', '').replace(']', '');
        } else if(element.type == 'select-one') {
            id = element.id.replace('select_', '').replace('attribute', '');
        } else {
            return;
        }
        if(element.value == '' || (typeof optionLabels[id][element.value] == 'undefined')) {
            return;
        }
        var label = optionLabels[id][element.value];
        for(var j = 0, linksLength = magicToolboxLinks.length; j < linksLength; j++) {
            if(magicToolboxLinks[j].firstChild.getAttribute('alt').replace(/(^\s+)|(\s+$)/g, "")/*.replace(/"/g, "'")*/.toLowerCase() == label) {
                allowMagicToolboxChange = false;
                MagicToolboxClickElement(magicToolboxLinks[j], 'MouseEvents', MagicToolbox_click);
                break;
            }
        }
    }

}

function MagicToolboxChangeSelector(a) {
    if(!allowMagicToolboxChange) {
        allowMagicToolboxChange = true;
        return;
    }
    var label = a.firstChild.getAttribute('alt').replace(/(^\s+)|(\s+$)/g, "").toLowerCase();
    var reloadPrice = false;
    for(var optionID in optionLabels) {
        for(var optionValue in optionLabels[optionID]) {
            if(optionLabels[optionID][optionValue] == label && MagicToolboxInArray(optionTitles[optionID], magicToolboxOptionTitles)) {
                var elementNames = ['options['+optionID+']', 'super_attribute['+optionID+']'];
                for(var elementNameIndex = 0, elementNamesLength = elementNames.length; elementNameIndex < elementNamesLength; elementNameIndex++) {
                    var elements = document.getElementsByName(elementNames[elementNameIndex]);
                    for(var i = 0, l = elements.length; i < l; i++) {
                        if(elements[i].type == 'radio') {
                            if(elements[i].value == optionValue) {
                                var radios = document.getElementsByName(elements[i].name);
                                for(var radioIndex = 0, radiosLength = radios.length; radioIndex < radiosLength; radioIndex++) {
                                    radios[radioIndex].checked = false;
                                }
                                elements[i].checked = true;
                                allowMagicToolboxChange = false;
                                MagicToolboxClickElement(elements[i], 'Event', 'click');
                                return;
                            }
                        } else if(elements[i].type == 'select-one') {
                            if(elements[i].options) {
                                for(var j = 0, k = elements[i].options.length; j < k; j++) {
                                    if(elements[i].options[j].value == optionValue) {
                                        elements[i].value = elements[i].options[j].value;
                                        elements[i].selectedIndex = j;
                                        allowMagicToolboxChange = false;
                                        MagicToolboxClickElement(elements[i], 'Event', 'change');
                                        return;
                                    }
                                }
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }
    }
}

function MagicToolboxInArray(needle, haystack) {
    var o = {};
    for(var i=0, l=haystack.length; i<l; i++) {
        o[haystack[i]] = '';
    }
    if(needle in o) {
        return true;
    }
    return false;
}

function MagicToolboxChangeOptionConfigurable(element, optionTitle) {

    if(!allowMagicToolboxChange) {
        allowMagicToolboxChange = true;
        return;
    }

    var attributeId = element.id.replace(/[a-z]*/, '');

    if(associatedImagesCount) {

        if(typeof choosedOptions[attributeId] != 'undefined') {
            delete choosedOptions[attributeId];
        }

        //clear child elements in choosedOptions
        if(element.childSettings) {
            for(var i=0,l= element.childSettings.length;i<l;i++){
                var childAttributeId = element.childSettings[i].id.replace(/[a-z]*/, '');
                if(typeof choosedOptions[childAttributeId] != 'undefined') {
                    delete choosedOptions[childAttributeId];
                }
            }
        }

        var associatedProductId = MagicToolboxFindProduct(attributeId, element.value);
        //add new option or replace one
        choosedOptions[attributeId] = element.value;
        var configurableProductId = spConfig.config.productId;
        if(typeof associatedImages['img'+associatedProductId] != 'undefined') {
            MagicZoom.update('MagicZoomImage'+configurableProductId, associatedImages['img'+associatedProductId], associatedImages['thumb'+associatedProductId]);
            return;
        }
    }

    MagicToolboxChangeOption(element, optionTitle);

}

function MagicToolboxFindProduct(attributeId, optionId) {

    for(var i in optionProductIDs[attributeId][optionId]) {
        //product associated with current option
        var pId = optionProductIDs[attributeId][optionId][i];
        for(var attrId in choosedOptions) {
            //selected option's ID
            var optId = choosedOptions[attrId];
            for(var j in optionProductIDs[attrId][optId]) {
                if(pId == optionProductIDs[attrId][optId][j]) {
                    optId = null;
                    break;
                }
            }
            if(optId != null) {
                pId = null;
                break;
            }
        }
        if(pId != null) {
            return pId;
        }
    }
    return optionProductIDs[attributeId][optionId][0];

}

