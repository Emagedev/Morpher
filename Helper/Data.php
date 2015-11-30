<?php

class Emagedev_RussianLanguage_Helper_Data extends Mage_Core_Helper_Abstract {
    const MORPHER_URI = "http://api.morpher.ru/WebService.asmx/GetXml";
    const MORPHER_MULTI = 'множественное';

    protected $_hasModel = false;

    public function inflectByNumber($number, $phrase)
    {
        $exclusions = array(11, 12);

        if(!in_array($number, $exclusions)) {
            $lower = $number % 10;

            if($lower == 1) {
                return $number . ' ' . $phrase;
            } elseif (in_array($lower, array(2,3,4))) {
                return $number . ' ' . $this->inflect($phrase, 'Р', false);
            }
        }

        return $number . ' ' . $this->inflect($phrase, 'Р', true);
    }

    public function inflect($phrase, $inflection, $multi = false)
    {
        $inflectedPhrase = $this->_tryModel($phrase, $inflection, $multi);
        if(empty($inflectedPhrase)) {
            $inflectedPhrase = $this->_tryMorpher($phrase, $inflection, $multi)[0];
            $this->_saveInflection($phrase, $inflection, $multi, $inflectedPhrase);
        }

        return $inflectedPhrase;
    }

    protected function _tryModel($phrase, $inflection, $multi = false)
    {
        /** @var Emagedev_RussianLanguage_Model_Inflection $model */
        $model = Mage::getModel('emagedev_russian/inflection');

        $multi = (int)$multi;

        /** @var Emagedev_RussianLanguage_Model_Resource_Inflection_Collection $collection */
        $collection = $model->getCollection()
            ->addFieldToSelect('inflected_phrase')
            ->addFieldToFilter('phrase', array('eq' => $phrase))
            ->addFieldToFilter('inflection', array('eq' => $inflection))
            ->addFieldToFilter('multi', array('eq' => $multi));

        $collection->load();

        foreach($collection->getItems() as $_item) {
            $this->_hasModel = true;
            return $_item->getInflectedPhrase();
        }

        return false;
    }

    protected function _saveInflection($phrase, $inflection, $multi = false, $inflectedPhrase)
    {
        /** @var Emagedev_RussianLanguage_Model_Inflection $model */
        $model = Mage::getModel('emagedev_russian/inflection');
        $model->setData(array(
            'phrase' => $phrase,
            'inflection' => $inflection,
            'multi' => $multi,
            'inflected_phrase' => $inflectedPhrase
        ));

        $model->save();
    }

    protected function _tryMorpher($phrase, $inflection, $multi = false)
    {
        $readyPhrase = $phrase;
        try {
            // Magento encode doesn't work somehow.
            $urlPhrase = urlencode($phrase);
            $path = self::MORPHER_URI . '?s=' . $urlPhrase;

            $curl = new Varien_Http_Adapter_Curl();
            $curl->setConfig(array(
                'timeout'   => 15,
                'userpwd'   => 'omedrec:zaq12wsxcvf'
            ));
            $curl->write(Zend_Http_Client::GET, $path, '1.0');
            $response = $curl->read();
            $headerSize = $curl->getInfo(CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            $curl->close();
            $xml = new SimpleXMLElement($body);

            if($multi) {
                $readyPhrase = $xml->{'множественное'}->{$inflection};
            } else {
                $readyPhrase = $xml->{$inflection};
            }
        }
        catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        if(empty($readyPhrase)) {
            $readyPhrase = $phrase;
        }

        return $readyPhrase;
    }
}