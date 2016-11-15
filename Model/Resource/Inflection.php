<?php
 
class Emagedev_RussianLanguage_Model_Resource_Inflection extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('emagedev_russian/inflection', 'id');
    }

}