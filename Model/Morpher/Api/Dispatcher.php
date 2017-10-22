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
 * Class Emagedev_RussianLanguage_Model_Morpher_Api_Dispatcher
 *
 * Dispatcher model processing raw result coming from REST API
 * create new inflection-form models and saving them to cache
 */
class Emagedev_RussianLanguage_Model_Morpher_Api_Dispatcher
{
    const MULTIPLE_FORM = 'множественное';

    /**
     * Process XML document from API
     *
     * @param SimpleXMLElement $document
     * @param string           $phrase
     * @param array            $flags
     */
    public function dispatchXmlData(SimpleXMLElement $document, $phrase, $flags)
    {
        $flagsKey = implode(',', $flags);

        $this->dispatchInflectionNodes($document, $phrase, false, $flagsKey);
    }

    /**
     * Work with XML nodes - create new model for each inflection
     * and dig down the XML tree for multiple forms
     *
     * @param SimpleXMLElement $parent
     * @param string           $phrase
     * @param bool             $multipleForm
     * @param string           $flagsKey
     *
     * @return array Array of created forms
     */
    protected function dispatchInflectionNodes(SimpleXMLElement $parent, $phrase, $multipleForm = false, $flagsKey = '')
    {
        $forms = array();

        /** @var SimpleXMLElement $node */
        foreach ($parent->children() as $node) {
            if (in_array($node->getName(), $this->getDataHelper()->getInflections())) {
                $newForm = $this->saveNewForm(
                    $phrase,
                    $node->getName(),
                    $node->__toString(),
                    $multipleForm,
                    $flagsKey
                );

                $forms[] = $newForm;
                continue;
            }

            if ($node->getName() == self::MULTIPLE_FORM) {
                $multipleForms = $this->dispatchInflectionNodes($node, $phrase, true, $flagsKey);
                $forms = array_merge($forms, $multipleForms);
            }
        }

        return $forms;
    }

    /**
     * Create and save new inflection form for phrase
     *
     * @param string $phrase
     * @param string $inflection
     * @param bool   $multipleForm
     * @param string $result
     * @param string $flagsKey
     *
     * @return Emagedev_RussianLanguage_Model_Inflection
     */
    protected function saveNewForm($phrase, $inflection, $multipleForm, $result, $flagsKey = '')
    {
        /** @var Emagedev_RussianLanguage_Model_Inflection $newForm */
        $newForm = Mage::getModel('emagedev_russian/inflection');
        $newForm
            ->setPhrase($phrase)
            ->setInflection($inflection)
            ->setMulti($multipleForm)
            ->setInflectedPhrase($result)
            ->setFlagsKey($flagsKey)
            ->save();

        return $newForm;
    }

    /**
     * Get module data helper
     *
     * @return Emagedev_RussianLanguage_Helper_Data
     */
    protected function getDataHelper()
    {
        return Mage::helper('emagedev_russian');
    }
}