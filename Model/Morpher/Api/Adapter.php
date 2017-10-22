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
 * Class Emagedev_RussianLanguage_Model_Morpher_Api_Adapter
 *
 * Adapter model provides works with Morpher.ru rest API through cURL requests
 */
class Emagedev_RussianLanguage_Model_Morpher_Api_Adapter
{
    const URI = "http://api.morpher.ru/WebService.asmx/GetXml";

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