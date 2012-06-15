<?php

class MagicToolbox_MagicZoom_Block_Adminhtml_Settings extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        parent::__construct();
        $this->_controller = 'adminhtml_settings';
        $this->_blockGroup = 'magiczoom';
        $this->_headerText = Mage::helper('magiczoom')->__('Settings Manager');
        $this->setTemplate('magiczoom/settings.phtml');

    }

    protected function _prepareLayout() {

        $this->setChild('settings_grid', $this->getLayout()->createBlock('magiczoom/adminhtml_settings_grid', 'magiczoom.grid'));
        $this->setChild('custom_design_settings_form', $this->getLayout()->createBlock('magiczoom/adminhtml_settings_form', 'magiczoom.form'));
        return parent::_prepareLayout();

    }

    public function getAddCustomSettingsFormHtml() {

        $html = $this->getChildHtml('custom_design_settings_form');
        if(Mage::registry('magiczoom_custom_design_settings_form')) {
            return $html;
        } else {
            return '';
        }

    }

    public function getSettingsGridHtml() {

        return $this->getChildHtml('settings_grid');

    }

}
