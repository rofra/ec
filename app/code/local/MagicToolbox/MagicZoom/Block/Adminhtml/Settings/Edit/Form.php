<?php

class MagicToolbox_MagicZoom_Block_Adminhtml_Settings_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $model = Mage::registry('magiczoom_data');
        $data = $model->getData();

        //require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'core'.DS.'magiczoom.module.core.class.php');
        require_once(BP . str_replace('/', DS, '/app/code/local/MagicToolbox/MagicZoom/core/magiczoom.module.core.class.php'));

        $tool = new MagicZoomModuleCoreClass();

        if($data['value']) {
            $params = unserialize($data['value']);
            foreach($params  as $id => $value) {
                $tool->params->params[$id]['value'] = $value;
            }
        }

        $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
                                      'class' => 'magiczoomEditForm',
                                    ));
        $form->setUseContainer(true);
        $this->setForm($form);

        $gId = 0;
        foreach($this->getParamsMap() as $group => $ids) {
            $fieldset = $form->addFieldset('group'.$gId++, array('legend'=>Mage::helper('magiczoom')->__($group), 'class'=>'magiczoomFieldset'));
            foreach($ids as $id) {
                if($tool->type == 'standard' && $id == 'direction') continue;
                $config = array(
                    'label'     => Mage::helper('magiczoom')->__($tool->params->params[$id]['label']),
                    'name'      => $id,
                    'note'      => '',
                    'value'     => isset($tool->params->params[$id]['value'])?$tool->params->params[$id]['value']:$tool->params->params[$id]['default']
                );
                if(isset($tool->params->params[$id]['description'])) {
                    $config['note'] = $tool->params->params[$id]['description'];
                }
                if(($tool->params->params[$id]['type'] != 'array') && isset($tool->params->params[$id]['values'])) {
                    if(!empty($config['note'])) $config['note'] .= "<br />";
                    $config['note'] .= "(allowed values: ".implode(", ",$tool->params->params[$id]['values']).")";
                }
                switch($tool->params->params[$id]['type']) {
                    case 'text':
                    case 'num':
                        $type = 'text';
                        break;
                    case 'array':
                        switch($tool->params->params[$id]['subType']) {
                            case 'select':
                                if($id == 'template') {
                                    $type = 'select';
                                    break;
                                }
                            case 'radio':
                                $type = 'radios';
                                $config['style'] = 'margin-right: 5px;';
                                break;
                            default:
                                $type = 'text';
                        }
                        $config['values'] = array();
                        foreach($tool->params->params[$id]['values'] as $v) {
                            $config['values'][] = array('value'=>$v, 'label'=>$v);
                        }
                        break;
                    default:
                        $type = 'text';
                }
                $fieldset->addField($id, $type, $config);
            }
        }

        return parent::_prepareForm();
    }

    protected function _afterToHtml($html) {

        $html .= '
<script type="text/javascript">

    getElementsByClass = function(classList, node) {
        var node = node || document;
        if(node.getElementsByClassName) {
            return node.getElementsByClassName(classList);
        } else {
            var nodes = node.getElementsByTagName("*"),
            nodesLength = nodes.length,
            classes = classList.split(/\s+/),
            classesLength = classes.length,
            result = [], i,j;
            for(i = 0; i < nodesLength; i++) {
                for(j = 0; j < classesLength; j++)  {
                    if(nodes[i].className.search("\\\\b" + classes[j] + "\\\\b") != -1) {
                        result.push(nodes[i]);
                        break;
                    }
                }
            }
            return result;
        }
    }

    var fieldsets = getElementsByClass("magiczoomFieldset");
    var header = null;
    var buttons = null;
    for(var i = 0, l = fieldsets.length; i < l; i++) {
        header = fieldsets[i].previousSibling;
        while(header.nodeType!=1) {
            header = header.previousSibling;
        }
        header.style.cursor = "pointer";
        buttons = getElementsByClass("form-buttons", header);
        buttons[0].innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        buttons[0].className += " fieldsetOpen";
        header.onclick = function() {
            var buttons = getElementsByClass("form-buttons", this);
            var fieldset = this.nextSibling;
            while(fieldset.nodeType!=1) {
                fieldset = fieldset.nextSibling;
            }
            if(buttons[0].className.match(/\bfieldsetOpen\b/)) {
                buttons[0].className = buttons[0].className.replace(/\bfieldsetOpen\b/, "fieldsetClose");
                fieldset.style.display = "none";
                this.style.marginBottom = "15px";
            } else {
                buttons[0].className = buttons[0].className.replace(/\bfieldsetClose\b/, "fieldsetOpen");
                fieldset.style.display = "block";
                this.style.marginBottom = "0px";
            }
            return false;
        }
    }

    initOptionsValidation("template", "magicscroll");

</script>
';

        return parent::_afterToHtml($html);

    }

    function getParamsMap() {
		return array(
				'General' => array(
					'template',
					'magicscroll'
				),
				'Positioning and Geometry' => array(
					'thumb-max-width',
					'thumb-max-height',
					'category-thumb-max-width',
					'category-thumb-max-height',
					'square-images',
					'zoom-width',
					'zoom-height',
					'zoom-position',
					'zoom-align',
					'zoom-distance'
				),
				'Effects' => array(
					'opacity',
					'opacity-reverse',
					'zoom-fade',
					'zoom-window-effect',
					'zoom-fade-in-speed',
					'zoom-fade-out-speed',
					'fps',
					'smoothing',
					'smoothing-speed'
				),
				'Multiple images' => array(
					'selector-max-width',
					'selector-max-height',
					'show-selectors-on-category-page',
					'category-selector-max-width',
					'category-selector-max-height',
					'use-individual-titles',
					'selectors-margin',
					'selectors-change',
					'selectors-class',
					'preload-selectors-small',
					'preload-selectors-big',
					'selectors-effect',
					'selectors-effect-speed',
					'selectors-mouseover-delay'
				),
				'Initialization' => array(
					'initialize-on',
					'click-to-activate',
					'click-to-deactivate',
					'show-loading',
					'loading-msg',
					'loading-opacity',
					'loading-position-x',
					'loading-position-y',
					'entire-image'
				),
				'Title and Caption' => array(
					'show-title'
				),
				'Miscellaneous' => array(
					'use-effect-on-product-page',
					'use-effect-on-category-page',
					'link-to-product-page',
					'option-associated-with-images',
					'load-associated-product-images',
					'ignore-magento-css',
					'show-message',
					'message',
					'right-click',
					'image-magick-path'
				),
				'Zoom mode' => array(
					'disable-zoom',
					'always-show-zoom',
					'drag-mode',
					'move-on-click',
					'x',
					'y',
					'preserve-position',
					'fit-zoom-window'
				),
				'Hint' => array(
					'hint',
					'hint-text',
					'hint-position',
					'hint-opacity'
				),
				'Scroll' => array(
					'scroll-style',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
		);
	}

}
