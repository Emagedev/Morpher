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
 * @subpackage Model
 * @author     Dmitry Burlakov <dantaeusb@icloud.com>
 */

/**
 * Class Emagedev_Morpher_Model_Inflection
 *
 * @method $this setPhrase(string $phrase)
 * @method string getPhrase()
 * @method $this setInflection(string $inflection)
 * @method string getInflection()
 * @method $this setMulti(bool $multipleForm)
 * @method bool getMulti()
 * @method $this setInflectedPhrase(string $phrase)
 * @method string getInflectedPhrase()
 */
class Emagedev_Morpher_Model_Inflection extends Mage_Core_Model_Abstract
{
    const FLAGS_KEY = 'flags';

    protected $flagsSerialized = true;

    protected function _construct()
    {
        $this->_init('morpher/inflection');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getInflectedPhrase();
    }

    /**
     * Serialize flags before sabe
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $flags = $this->getData(self::FLAGS_KEY);

        if ($flags && !$this->flagsSerialized) {
            $this->setData(self::FLAGS_KEY, Mage::helper('morpher')->serializeFlags($flags));
        }

        return parent::_beforeSave();
    }

    /**
     * Get array of flags that added on inflection fetch
     *
     * @return array
     */
    public function getFlags()
    {
        $flags = $this->getData(self::FLAGS_KEY);

        if (!empty($flags) && $this->flagsSerialized) {
            $flags = Mage::helper('morpher')->unserializeFlags($flags);
            $this->setFlags($flags);
        }

        return $flags;
    }

    /**
     * Set flags that added on inflection fetch
     *
     * @param array $flags
     *
     * @return $this
     */
    public function setFlags($flags)
    {
        if (is_array($flags)) {
            $this->flagsSerialized = false;
        }

        if (is_string($flags)) {
            $this->flagsSerialized = true;
        }

        $this->setData(self::FLAGS_KEY, $flags);

        return $this;
    }
}