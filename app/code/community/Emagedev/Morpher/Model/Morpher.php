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
 * Class Emagedev_Morpher_Model_Morpher
 */
class Emagedev_Morpher_Model_Morpher
{
    /**
     * Inflect word, first try cache, then API
     *
     * @param string $phrase
     * @param string $inflection
     * @param bool   $multi
     * @param array  $flags
     *
     * @return string
     */
    public function inflect($phrase, $inflection, $multi = false, $flags = array())
    {
        $inflectedPhrase = $this->cacheLookup($phrase, $inflection, $multi, $flags);

        if (!$inflectedPhrase) {
            $this->runMorpher($phrase, $flags);
            $inflectedPhrase = $this->cacheLookup($phrase, $inflection, $multi, $flags);
        }

        return $inflectedPhrase ? $inflectedPhrase->getInflectedPhrase() : $phrase;
    }

    /**
     * Try to find word in cache
     *
     * @param string $phrase
     * @param string $inflection
     * @param bool   $multi
     * @param array  $flags
     *
     * @return Emagedev_Morpher_Model_Inflection|false
     */
    protected function cacheLookup($phrase, $inflection, $multi = false, $flags = array())
    {
        /** @var Emagedev_Morpher_Model_Resource_Inflection_Collection $collection */
        $collection = Mage::getModel('morpher/inflection')->getCollection();
        $collection
            ->addFieldToFilter('phrase', $phrase)
            ->addFieldToFilter('inflection', $inflection)
            ->addFieldToFilter('multi', $multi ? 1 : 0)
            ->addFieldToFilter('flags', Mage::helper('morpher')->serializeFlags($flags))
            ->setPageSize(1);

        return count($collection) > 0 ? $collection->getFirstItem() : false;
    }

    /**
     * Run API request
     *
     * @param $phrase
     * @param $flags
     */
    protected function runMorpher($phrase, $flags)
    {
        /** @var Emagedev_Morpher_Model_Morpher_Api_Adapter $morpher */
        $morpher = Mage::getModel('morpher/morpher_api_adapter');
        $morpher->run($phrase, $flags);
    }
}
