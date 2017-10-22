<?php
/**
 * Emagedev extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * Copyright (C) Effdocs, LLC - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 *
 * This source file is proprietary and confidential
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Omedrec Startpage module to newer versions in the future.
 *
 * @copyright  Copyright (C) Effdocs, LLC
 * @license    http://www.binpress.com/license/view/l/45d152a594cd48488fda1a62931432e7
 */

/**
 * @category   Emagedev
 * @package    Emagedev_RussianLanguage
 * @subpackage Model
 * @author     Dmitry Burlakov <dantaeusb@icloud.com>
 */

/**
 * Class Emagedev_RussianLanguage_Model_Morpher
 */
class Emagedev_RussianLanguage_Model_Morpher
{
    /**
     * @param string $phrase
     * @param string $inflection
     * @param bool   $multi
     *
     * @return string
     */
    public function inflect($phrase, $inflection, $multi = false, $flags = array())
    {
        $inflectedPhrase = $this->cacheLookup($phrase, $inflection, $multi);
        if (empty($inflectedPhrase)) {
            $inflectedPhrase = $this->tryMorpher($phrase, $inflection, $multi)[0];
            $this->saveInflection($phrase, $inflection, $multi, $inflectedPhrase);
        }

        return $inflectedPhrase;
    }

    /**
     * @param string $phrase
     * @param string $inflection
     * @param bool   $multi
     * @param array  $flags
     *
     * @return Varien_Object
     */
    protected function cacheLookup($phrase, $inflection, $multi = false, $flags = array())
    {
        /** @var Emagedev_RussianLanguage_Model_Resource_Inflection_Collection $collection */
        $collection = Mage::getModel('emagedev_russian/inflection')->getCollection();
        $collection
            ->addFieldToFilter('phrase', $phrase)
            ->addFieldToFilter('inflection', $inflection)
            ->addFieldToFilter('multi', $multi);

        return $collection->getFirstItem();
    }

    protected function runMorpher($phrase, $flags)
    {
        /** @var Emagedev_RussianLanguage_Model_Morpher_Api_Adapter $morpher */
        $morpher = Mage::getModel('emagedev_russian/morpher_api_adapter');
        $morpher->run($phrase, $flags);
    }
}