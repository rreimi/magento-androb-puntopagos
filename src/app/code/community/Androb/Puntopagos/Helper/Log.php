<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:30 PM
 *
 * Puntopagos Module helper log
 *
 */
class Androb_Puntopagos_Helper_Log extends Mage_Core_Helper_Data {

    private $_orderLogEnabled;
    private $_debugLogEnabled;
    private $_logFolder = 'puntopagos';

    public function __construct() {
        $logBaseDir = Mage::getBaseDir('var');
        $logBaseDir .= DS . 'log'. DS . $this->_logFolder . DS;

        if (!file_exists($logBaseDir)) {
            mkdir($logBaseDir, 0775, true);
        }

        $this->_orderLogEnabled = Mage::getStoreConfig('payment/puntopagos/order_log');
        $this->_debugLogEnabled = Mage::getStoreConfig('payment/puntopagos/debug_mode');
    }

    public function logOrder($message, $orderId) {
        if ($this->_orderLogEnabled) {
            Mage::log($message, LOG_INFO, $this->_logFolder . DS . $orderId . '.log', true);
        }
    }

    public function logDebug($message, $orderId = null) {
        if ($orderId != null) {
            $this->logOrder($message, $orderId);
        }

        if ($this->_debugLogEnabled) {
            Mage::log($message, LOG_INFO, $this->_logFolder . DS . 'debug.log', true);
        }
    }
}
