<?php
/**
 *
 * Banktransfer payment type Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Androb_Puntopagos_Model_Method_Commercialcard_Source_PaymentOption
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 2,
                'label' => 'Tarjeta Presto',
                'sandbox' => false
            ),
            array(
                'value' => 10,
                'label' => 'Tarjeta Ripley',
                'sandbox' => true
            ),
            array(
                'value' => 17,
                'label' => 'Tarjeta Cencosud',
                'sandbox' => false
            ),
            array(
                'value' => 18,
                'label' => 'Tarjeta Paris',
                'sandbox' => false
            ),
            array(
                'value' => 19,
                'label' => 'Tarjeta Jumbo',
                'sandbox' => false
            ),

        );
    }
}