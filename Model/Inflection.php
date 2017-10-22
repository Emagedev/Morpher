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
 * Class Emagedev_RussianLanguage_Model_Inflection
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
class Emagedev_RussianLanguage_Model_Inflection extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('emagedev_russian/inflection');
    }
}