<?php

class Emagedev_RussianLanguage_Model_Resource_Inflection_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Init menu collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('emagedev_russian/inflection');
    }
}
