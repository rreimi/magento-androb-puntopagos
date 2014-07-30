<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:30 PM
 *
 * Puntopagos Module helper data
 *
 */
class Androb_Puntopagos_Helper_Data extends Mage_Core_Helper_Data {


    public function getKeyId() {
        return Mage::getStoreConfig('payment/puntopagos/key_id');
    }

    public function getKeySecret() {
        return Mage::getStoreConfig('payment/puntopagos/key_secret');
    }

    public function getGatewayUrl() {
        $sandboxMode = Mage::getStoreConfig('payment/puntopagos/sandbox_mode');
        if ($sandboxMode) {
            $url = Mage::getStoreConfig('payment/puntopagos/sandbox_url');
        } else {
            $url = Mage::getStoreConfig('payment/puntopagos/production_url');
        }

        //Check for slash at the end to normalize
        if (!$this->endsWith($url, '/')){
            $url .= '/';
        }
        return $url;
    }

    public function isGatewayActive() {
        return Mage::getStoreConfig('payment/puntopagos/active');
    }

    public function isPaymentMethodActive($code) {
        return Mage::getStoreConfig('payment/' . $code . '/active');
    }

    public function isSandboxMode() {
        return Mage::getStoreConfig('payment/puntopagos/sandbox_mode');
    }

    public function getGatewayOrderStatus() {
        return Mage::getStoreConfig('payment/puntopagos/order_status');
    }

    public function isCreateInvoiceActive() {
        return Mage::getStoreConfig('payment/puntopagos/create_invoice');
    }

    public function getPaymentIconBaseUrl() {
        $url = Mage::getStoreConfig('payment/puntopagos/icon_url');

        if (empty($url)) {
            $url =Mage::getBaseUrl('media', false) . 'puntopagos/';
        }

        //Check for slash at the end to normalize
        if (!$this->endsWith($url, '/')){
            $url .= '/';
        }
        return $url;
    }

    /**
     * Return active payment options for current method
     *
     * @param $code string payment method code
     * @return null if no options, string if one option, array if multiple
     */
    public function getActivePaymentOptions($code) {
        $result = array();
        $options = Mage::getStoreConfig('payment/' . $code . '/payment_options');

        if ($options != null) {
            $result = explode(',', $options);
        }

        return $result;
    }

    private function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

}
