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
 * @subpackage Test
 * @author     Dmitry Burlakov <dantaeusb@icloud.com>
 */

/**
 * Class Emagedev_RussianLanguage_Test_Helper_Data
 */
class Emagedev_RussianLanguage_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    protected $helperAlias = 'emagedev_russian';

    /**
     * Get helper model to test
     *
     * @return Emagedev_RussianLanguage_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper($this->helperAlias);
    }

    /**
     * Check is inflection request for numbers working correctly
     *
     * @param string $word
     * @param int    $number
     *
     * @test
     * @dataProvider dataProvider
     * @loadFixture
     */
    public function checkNumberInflection($word, $number)
    {
        $morpher = $this->mockModel('emagedev_russian/morpher', array('tryMorpher'));

        $morpher
            ->expects($this->never())
            ->method('tryMorpher');

        $morpher->replaceByMock('model');

        $result = $this->getHelper()->inflectWordByNumber($number, $word, true);
        $this->assertEquals($this->expected('auto')->getWord(), $result);
    }

    /**
     * Check is translations using another helper working correctly
     *
     * ! Translations should be installed (probably)
     *
     * @param bool|string $translate translate or not (may specify helper with string)
     *
     * @test
     * @dataProvider dataProvider
     * @loadFixture
     */
    public function checkTranslation($translate)
    {
        Mage::app()->getTranslator()
            ->setLocale(Mage_Core_Model_Locale::DEFAULT_LOCALE)
            ->init('adminhtml', true);

        $morpher = $this->mockModel('emagedev_russian/morpher', array('inflect'));

        $morpher
            ->expects($this->any())
            ->method('inflect')
            ->will($this->returnArgument(0));

        $morpher->replaceByMock('model');

        $result = $this->getHelper()->inflectWord(
            'testphrasethatwillnottranslateonothermodules', Emagedev_RussianLanguage_Helper_Data::NOMINATIVE,
            false, array(), $translate
        );

        $this->assertEquals($this->expected('auto')->getResult(), $result);
    }

    /**
     * Check is auth string combined correctly
     *
     * @test
     * @loadFixture
     */
    public function checkAuth()
    {
        $this->assertEquals($this->expected()->getResult(), $this->getHelper()->getAuthString());
    }
}