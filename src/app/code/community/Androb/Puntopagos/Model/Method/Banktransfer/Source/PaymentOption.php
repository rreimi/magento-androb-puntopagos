<?php
/**
 *
 * Banktransfer payment type Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Androb_Puntopagos_Model_Method_Banktransfer_Source_PaymentOption
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => 'Banco Santander',
                'sandbox' => false
            ),
            array(
                'value' => 4,
                'label' => 'Banco de Chile',
                'sandbox' => false
            ),
            array(
                'value' => 5,
                'label' => 'BCI',
                'sandbox' => false
            ),
            array(
                'value' => 6,
                'label' => 'TBanc',
                'sandbox' => false
            ),
            array(
                'value' => 7,
                'label' => 'Banco Estado',
                'sandbox' => false
            ),
            array(
                'value' => 16,
                'label' => 'Banco BBVA',
                'sandbox' => false
            ),
        );
    }
}