controllers<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/3/14
 * Time: 1:13 AM
 * 
 * 
 */

class Androb_Puntopagos_Block_Banktransfer_Form extends Androb_Puntopagos_Block_PaymentForm {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('puntopagos/banktransfer/form.phtml');
    }

    public function getAvailablePaymentOptions($code) {
        /** @var Androb_Puntopagos_Helper_Data $helper */
        $helper = Mage::helper('puntopagos');

        $activeOptions = $helper->getActivePaymentOptions($code);

        $isSandboxMode = $helper->isSandboxMode();

        $sourceModel = Mage::getModel('puntopagos/method_banktransfer_source_paymentOption');
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