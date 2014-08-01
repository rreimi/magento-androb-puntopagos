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

    /**
     * Create log for specific order in separate file and append message
     *
     * @param $message
     * @param $orderId
     */
    private function logOrder($message, $orderId) {
        if ($this->_orderLogEnabled) {
            Mage::log($message, LOG_INFO, $this->_logFolder . DS . $orderId . '.log', true);
        }
    }

    /**
     * Create debug log and order log if orderId if given
     * Wrapper for only use one log method
     *
     * @param $message
     * @param null $orderId
     */
    public function logDebug($message, $orderId = null) {
        if ($orderId != null) {
            $this->logOrder($message, $orderId);
        }

        if ($this->_debugLogEnabled) {
            Mage::log($message, LOG_INFO, $this->_logFolder . DS . 'debug.log', true);
        }
    }
}
