<?php
/**
 * Emagedev extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Emagedev RussianLanguage module to newer versions in the future.
 *
 * @copyright  Copyright (C), emagedev.com
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
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
     *
     * @return array
     */
    public function dispatchXmlData(SimpleXMLElement $document, $phrase, $flags)
    {
        $flagsKey = implode(',', $flags);

        $forms = $this->dispatchInflectionNodes($document, $phrase, false, $flagsKey);

        return $forms;
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
                    $multipleForm,
                    $node->__toString(),
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
            ->setFlags($flagsKey)
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