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

class Androb_Puntopagos_Helper_Data extends Mage_Core_Helper_Data {

    /**
     * Return puntopagos api key id
     *
     * @return string
     */
    public function getKeyId() {
        return Mage::getStoreConfig('payment/puntopagos/key_id');
    }

    /**
     * Return puntopagos api key secret
     *
     * @return string
     */
    public function getKeySecret() {
        return Mage::getStoreConfig('payment/puntopagos/key_secret');
    }

    /**
     * Return gateway endpoint based on enviroment configuration (sandbox/production)
     *
     * @return string
     */
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

    /**
     * Check if puntopagos gateway is active
     *
     * @return mixed
     */
    public function isGatewayActive() {
        return Mage::getStoreConfig('payment/puntopagos/active');
    }

    /**
     * Check if puntopagos method given by code is enabled
     *
     * @param $code
     * @return mixed
     */
    public function isPaymentMethodActive($code) {
        return Mage::getStoreConfig('payment/' . $code . '/active');
    }

    /**
     * Check curren enviroment
     *
     * @return boolean true is sandbox is active
     */
    public function isSandboxMode() {
        return Mage::getStoreConfig('payment/puntopagos/sandbox_mode');
    }

    /**
     * @deprecated
     *
     * Return configured order status
     *
     * @return mixed
     */
    public function getGatewayOrderStatus() {
        return Mage::getStoreConfig('payment/puntopagos/order_status');
    }

    /**
     * Check if automatic invoice for success orders is active
     *
     * @return mixed
     */
    public function isCreateInvoiceActive() {
        return Mage::getStoreConfig('payment/puntopagos/create_invoice');
    }

    /**
     * Get payment options icon base url
     *
     * @return string
     */
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
