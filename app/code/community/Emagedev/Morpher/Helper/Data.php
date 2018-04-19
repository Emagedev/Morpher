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
 * the Emagedev Morpher module to newer versions in the future.
 *
 * @copyright  Copyright (C), emagedev.com
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

/**
 * @category   Emagedev
 * @package    Emagedev_Morpher
 * @subpackage Helper
 * @author     Dmitry Burlakov <dantaeusb@icloud.com>
 */

/**
 * Class Emagedev_Morpher_Helper_Data
 *
 * Contains all usable constants for API,
 * and lot of useful interfaces
 */
class Emagedev_Morpher_Helper_Data extends Mage_Core_Helper_Data
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

    /**
     * Get array of possible inflections of the word
     *
     * @return array
     */
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

    /**
     * Get available helper flags
     *
     * @return array
     */
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

    /**
     * Inflect some phrase by number
     *
     * @param int         $number
     * @param string      $phrase
     * @param bool        $keepNumber shall final (returned) phrase contains number in front of it
     * @param bool|string $translate  shall given phrase be translated before processing, if string
     *                                provided, uses string as helper code
     *
     * @return string
     */
    public function inflectWordByNumber($number, $phrase, $keepNumber = false, $translate = false)
    {
        return $phrase;

        $meaningNumber = abs($number % 100);

        if ($meaningNumber != 0 && ($meaningNumber < 11 || $meaningNumber > 19)) {
            $lower = $meaningNumber % 10;

            if ($lower == 1) {
                $word = $this->inflectWord($phrase, self::NOMINATIVE, false, array(), $translate);
            } elseif (in_array($lower, array(2, 3, 4))) {
                $word = $this->inflectWord($phrase, self::GENITIVE, false, array(), $translate);
            } else {
                $word = $this->inflectWord($phrase, self::GENITIVE, true, array(), $translate);
            }
        } else {
            $word = $this->inflectWord($phrase, self::GENITIVE, true, array(), $translate);
        }

        if ($keepNumber) {
            return $number . ' ' . $word;
        }

        return $word;
    }

    /**
     * Inflect some name (same as any phrase, but with helper flag, see API docs
     *
     * @param string $name
     * @param string $inflection
     * @param array  $flags
     *
     * @return string
     */
    public function inflectName($name, $inflection, $flags = array())
    {
        return $name;

        return $this->inflectWord($name, $inflection, array_merge($flags, array(self::FLAG_NAME)));
    }

    /**
     * Inflect male name
     *
     * @param string $name
     * @param string $inflection
     * @param array  $flags
     *
     * @return string
     */
    public function inflectMaleName($name, $inflection, $flags = array())
    {
        return $name;

        return $this->inflectWord(
            $name, $inflection, false, array_merge($flags, array(self::FLAG_NAME, self::FLAG_MASCULINE))
        );
    }

    /**
     * Inflect female name
     *
     * @param string $name
     * @param string $inflection
     * @param array  $flags
     *
     * @return string
     */
    public function inflectFemaleName($name, $inflection, $flags = array())
    {
        return $name;

        return $this->inflectWord($name, $inflection, false, array_merge($flags, array(self::FLAG_NAME, self::FLAG_FEMININE)));
    }

    /**
     * Run inflection
     *
     * @param string      $phrase
     * @param string      $inflection
     * @param bool        $multi     multiple form
     * @param array       $flags     additional flags for more correct inflections, on top of the file
     * @param bool|string $translate shall given phrase be translated before processing, if string
     *                               provided, uses string as helper code
     *
     * @return string
     */
    public function inflectWord($phrase, $inflection, $multi = false, $flags = array(), $translate = false)
    {
        return $phrase;

        if ($translate === true || is_string($translate)) {
            /** @var Mage_Core_Helper_Data $translator */
            $translator = $this;
            if (is_string($translate)) {
                $translator = Mage::helper($translate);
            }

            try {
                $phrase = $translator->__($phrase);
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::log('Seems like morpher module can\'t find translation helper for ' . $translator);
            }
        }

        try {
            return $this->getMorpher()->inflect($phrase, $inflection, $multi, $flags);
        } catch (Exception $e) {
            Mage::logException($e);
            return $phrase;
        }
    }

    /**
     * Get HTTP auth string or false if not set
     *
     * @return bool|string
     */
    public function getAuthString()
    {
        $login = Mage::getStoreConfig('morpher_api/general/login');

        if ($login) {
            $password = Mage::getStoreConfig('morpher_api/general/password');

            if ($password) {
                return $login . ':' . $password;
            }
        }

        return false;
    }

    /**
     * Serialize flags for saving to db
     *
     * @param array $flags
     *
     * @return string
     */
    public function serializeFlags($flags)
    {
        if (!is_array($flags) || empty($flags)) {
            return '';
        }

        return serialize($flags);
    }

    /**
     * Unerialize flags that saved to db
     *
     * @param array $flags
     *
     * @return string
     */
    public function unserializeFlags($flags)
    {
        if (!is_string($flags) || empty($flags)) {
            return $flags;
        }

        return unserialize($flags);
    }

    /**
     * @return Emagedev_Morpher_Model_Morpher
     */
    protected function getMorpher()
    {
        return Mage::getModel('morpher/morpher');
    }
}