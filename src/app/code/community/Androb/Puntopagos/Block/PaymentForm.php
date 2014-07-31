<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/6/14
 * Time: 3:33 PM
 *
 *
 */

abstract class Androb_Puntopagos_Block_PaymentForm extends Mage_Payment_Block_Form {

    /** @var $optionSourceModel String - The model name used to fetch the payment options */
    protected $_optionSourceModel;

    protected function getAvailablePaymentOptions($code) {
        /** @var Androb_Puntopagos_Helper_Data $helper */
        $helper = Mage::helper('puntopagos');

        $activeOptions = $helper->getActivePaymentOptions($code);

        $isSandboxMode = $helper->isSandboxMode();

        $sourceModel = Mage::getModel('puntopagos/' . $this->_optionSourceModel);
        $options = $sourceModel->toOptionArray();

        $result = array();

        $iconBaseUrl = $helper->getPaymentIconBaseUrl();

        foreach ($options as $option) {

            if ($isSandboxMode) {
                if ($option['sandbox'] != true) {
                    continue;
                }
            }

            if (in_array($option['value'], $activeOptions) ) {
                $option['icon_url'] = $iconBaseUrl . 'mp' . $option['value'] . '.gif';
                $result[] = $option;
            }
        }

        return $result;
    }
}