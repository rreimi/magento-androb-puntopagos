<?php
/**
 * Androb_Puntopagos Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category Androb
 * @package Puntopagos
 * @author Robert Reimi <robert.reimi@gmail.com>
 * @copyright Androb (www.androb.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


class Androb_Puntopagos_Model_Method_Webpay_Source_PaymentOption
{
    /**
     * Return posible payment options for webpay method
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 3,
                'label' => 'Webpay',
                'sandbox' => true
            ),
            array(
                'value' => 13,
                'label' => 'Webpay Transbank',
                'sandbox' => false
            ),
        );
    }
}