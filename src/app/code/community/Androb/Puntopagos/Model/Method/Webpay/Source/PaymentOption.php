<?php
/**
 *
 * Banktransfer payment type Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Androb_Puntopagos_Model_Method_Webpay_Source_PaymentOption
{
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