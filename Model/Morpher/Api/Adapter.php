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
 * Class Emagedev_RussianLanguage_Model_Morpher_Api_Adapter
 *
 * Adapter model provides works with Morpher.ru rest API through cURL requests
 */
class Emagedev_RussianLanguage_Model_Morpher_Api_Adapter
{
    const URI = "https://ws3.morpher.ru/russian/declension";

    /**
     * Create new request and dispatch data
     *
     * @param string $phrase
     * @param array  $flags
     *
     * @return string
     */
    public function run($phrase, $flags = array())
    {
        $readyPhrase = $phrase;
        try {
            $curl = new Varien_Http_Adapter_Curl();
            $curl->setConfig(
                array(
                    'timeout' => 15,
                    'userpwd' => 'omedrec:zaq12wsxcvf'
                )
            );
            $curl->write(
                Zend_Http_Client::GET,
                $this->getRequestUri($phrase, $flags),
                1.1);
            $response = $curl->read();

            $headerSize = $curl->getInfo(CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            $curl->close();
            $xml = new SimpleXMLElement($body);

            $this->getDispatcher()->dispatchXmlData($xml, $flags);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        if (empty($readyPhrase)) {
            $readyPhrase = $phrase;
        }

        return $readyPhrase;
    }

    /**
     * Get Zend URI model to combine request URL
     *
     * @return Zend_Uri_Http
     */
    protected function getUriModel()
    {
        return Zend_Uri_Http::fromString(self::URI);
    }

    /**
     * Prepare URL string with combined request data
     *
     * @param string $phrase
     * @param array  $flags
     *
     * @return string
     */
    protected function getRequestUri($phrase, $flags = array())
    {
        $params = array(
            's' => $phrase,
        );

        if (!empty($flags)) {
            $availableFlags = $this->getDataHelper()->getAvailableFlags();

            foreach ($flags as $flag) {
                if (!in_array($flag, $availableFlags)) {
                    Mage::log($this->getDataHelper()->__('Flag "%s" is invalid, see http://morpher.ru/ws3/'), Zend_Log::ERR);
                    unset($flags[$flag]);
                }
            }
        }

        if (!empty($flags)) {
            $params['flags'] = implode(',', $flags);
        }

        $uriModel = $this->getUriModel();
        $uriModel->setQuery($params);

        return $uriModel->getUri();
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

    /**
     * Get dispatcher to work with API response
     *
     * @return Emagedev_RussianLanguage_Model_Morpher_Api_Dispatcher
     */
    protected function getDispatcher()
    {
        return Mage::getModel('emagedev_russian/morpher_api_dispatcher');
    }
}