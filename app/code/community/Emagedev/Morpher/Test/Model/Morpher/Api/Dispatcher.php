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
 * @subpackage Test
 * @author     Dmitry Burlakov <dantaeusb@icloud.com>
 */

/**
 * Class Emagedev_Morpher_Test_Model_Morpher_Api_Dispatcher
 */
class Emagedev_Morpher_Test_Model_Morpher_Api_Dispatcher extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Check is example response dispatched correctly
     *
     * @param string $phrase
     * @param string $file
     * @param array  $flags
     *
     * @dataProvider dataProvider
     * @test
     */
    public function checkXmlProcessing($phrase, $file, $flags = array())
    {
        $filePath = __DIR__ . DS . '_data' . DS . $file;
        $expectRaw = file_get_contents($filePath);
        $expectXmlObject = new SimpleXMLElement($expectRaw);

        $this->mockModel('morpher/inflection', array('save'))
            ->replaceByMock('model');

        $forms = $this->getModel()->dispatchXmlData($expectXmlObject, $phrase, $flags);

        /**
         * @var int $iterator
         * @var Emagedev_Morpher_Model_Inflection $form
         */
        foreach ($forms as $iterator => $form) {
            $expectations = $this->expected('auto')->getData();
            $found = false;

            foreach ($expectations as $expectation) {
                if ($form->getPhrase() == $expectation['phrase']
                    && $form->getInflection() == $expectation['inflection']
                    && $form->getMulti() == $expectation['multi']
                ) {
                    $found = true;
                    $this->assertSame($expectation, $form->getData());
                }
            }

            $this->assertTrue($found);
        }
    }

    /**
     * Get model for testing
     *
     * @return Emagedev_Morpher_Model_Morpher_Api_Dispatcher
     */
    protected function getModel()
    {
        return Mage::getModel('morpher/morpher_api_dispatcher');
    }
}