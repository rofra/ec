<?php

class MagicToolbox_MagicZoom_Model_Mysql4_Settings extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        $this->_init('magiczoom/settings', 'setting_id');

    }

}
