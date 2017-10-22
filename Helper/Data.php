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
 * @subpackage Helper
 * @author     Dmitry Burlakov <dantaeusb@icloud.com>
 */

/**
 * Class Emagedev_RussianLanguage_Helper_Data
 */
class Emagedev_RussianLanguage_Helper_Data extends Mage_Core_Helper_Abstract
{
    const NOMINATIVE = 'И';
    const GENITIVE = 'Р';
    const ACCUSATIVE = 'В';
    const DATIVE = 'Д';
    const INSTRUMENTAL = 'Т';
    const PREPOSITIONAL = 'П';
    const PREPOSITIONAL_WITH_PREFIX = 'П_о';
    const LOCATION = 'М';

    const FLAG_FEMININE = 'feminine';
    const FLAG_MASCULINE = 'masculine';
    const FLAG_ANIMATE = 'animate';
    const FLAG_INANIMATE = 'inanimate';
    const FLAG_COMMON = 'common';
    const FLAG_NAME = 'name';

    public function getInflections()
    {
        return array(
            self::NOMINATIVE,
            self::GENITIVE,
            self::ACCUSATIVE,
            self::DATIVE,
            self::INSTRUMENTAL,
            self::PREPOSITIONAL,
            self::PREPOSITIONAL_WITH_PREFIX,
            self::LOCATION
        );
    }

    public function getAvailableFlags()
    {
        return array(
            self::FLAG_FEMININE,
            self::FLAG_MASCULINE,
            self::FLAG_ANIMATE,
            self::FLAG_INANIMATE,
            self::FLAG_COMMON,
            self::FLAG_NAME
        );
    }

    public function inflectByNumber($number, $phrase)
    {
        return $number . ' ' . mb_strtolower($this->inflectWordByNumber($number, $phrase), 'UTF-8');
    }

    /**
     * @param int    $number
     * @param string $phrase
     * @param bool   $keepNumber
     *
     * @return string
     */
    public function inflectWordByNumber($number, $phrase, $keepNumber = false, $translate = true)
    {
        if ($translate) {
            $phrase = $this->__($phrase);
        }

        $meaningNumber = abs($number % 100);
        $word = $phrase;

        if ($meaningNumber < 11 || $meaningNumber > 19) {
            $lower = $meaningNumber % 10;

            if ($lower == 1) {
                $word = $this->inflect($phrase, self::NOMINATIVE, false);
            } elseif (in_array($lower, array(2, 3, 4))) {
                $word = $this->inflect($phrase, self::GENITIVE, false);
            }
        } else {
            $word = $this->inflect($phrase, self::GENITIVE, true);
        }

        if ($keepNumber) {
            return $number . ' ' . $word;
        }

        return $word;
    }

    public function inflectName($name, $inflection, $flags = array())
    {
        return $this->inflectWord($name, $inflection, array_merge($flags, array(self::FLAG_NAME)));
    }

    public function inflectMaleName($name, $inflection, $flags = array())
    {
        return $this->inflectWord($name, $inflection, array_merge($flags, array(self::FLAG_NAME, self::FLAG_MASCULINE)));
    }

    public function inflectFemaleName($name, $inflection, $flags = array())
    {
        return $this->inflectWord($name, $inflection, array_merge($flags, array(self::FLAG_NAME, self::FLAG_FEMININE)));
    }

    public function inflectWord($phrase, $inflection, $multi = false, $flags = array())
    {
        $this->getAdapter()->inflect($phrase, $inflection, $multi, $flags);
    }

    /**
     * @return Emagedev_RussianLanguage_Model_Morpher_Api_Adapter
     */
    protected function getAdapter()
    {
        return Mage::getModel('emagedev_russian/morpher_api_adapter');
    }
}