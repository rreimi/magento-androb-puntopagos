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

class Androb_Puntopagos_Model_Api {


    const TRANSACTION_STATUS_COMPLETED = '00';
    const TRANSACTION_STATUS_INCOMPLETED = '6';
    const TRANSACTION_STATUS_REJECTED = '';

    private $keyId;
    private $keySecret;
    private $gatewayUrl;

    public function __construct($args = array()) {
        //Arguments for testing purposes
        $this->keyId = (isset($args['keyId']))? $args['keyId'] : $this->_getHelper()->getKeyId();
        $this->keySecret = (isset($args['keySecret']))? $args['keySecret'] : $this->_getHelper()->getKeySecret();
        $this->gatewayUrl = (isset($args['gatewayUrl']))? $args['gatewayUrl'] : $this->_getHelper()->getGatewayUrl();

        $this->_getLogHelper()->logDebug('Initializing api for keyId: ' . $this->keyId);
        $this->_getLogHelper()->logDebug('Using gateway url: ' . $this->gatewayUrl);
    }

    /**
     * Register a new transaction with puntopagos gateway
     *
     * @param $trxId string             transaction id (magento order increment id)
     * @param $amount float             transaction amount
     * @param $paymentMethod string     payment option
     * @return object                   with transaction result
     *
     */
    public function createTransaction($trxId, $amount, $paymentMethod = null) {
        $this->_getLogHelper()->logDebug('Creating new transaction for order: ' . $trxId, $trxId);

        $operation = 'transaccion/crear';
        $url = $this->gatewayUrl . $operation;

        $this->_getLogHelper()->logDebug('Using gateway url: ' . $url);

        $amount = round($amount);

        //Get headers
        $amount = $this->_formatAmount($amount);
        $message = $operation."\n".$trxId."\n".$amount;
        $headers = $this->getHeaders($message);

        //Create data
        $data = new stdClass();
        $data->trx_id = $trxId;
        $data->monto = $this->_formatAmount($amount);

        if ($paymentMethod != null) {
            $data->medio_pago = $paymentMethod;
        }

        $data = json_encode($data);

        $this->_getLogHelper()->logDebug('Using headers: ' . json_encode($headers));
        $this->_getLogHelper()->logDebug('Using data: ' . $data);

        $result = $this->sendRequest($url, Zend_Http_Client::POST, $headers, $data);

        $this->_getLogHelper()->logDebug('Loaded result: ' . $result, $trxId);

        $result = json_decode($result);
        return $result;
    }

    /**
     * Retrieve a transaction from puntopagos gateway
     *
     * @param $token    string     puntopagos transaction token
     * @param $trxId    string     magento order incrementId
     * @param $amount   float      transaction amount
     * @return object   object with transaction result
     */
    public function getTransaction($token, $trxId, $amount) {

        $this->_getLogHelper()->logDebug('Loading transaction for token: ' . $token, $trxId);
        $this->_getLogHelper()->logDebug('Using trxId (orderId): ' . $trxId . ', amount: ' . $amount);

        /** Punto pagos does not accept decimals (chilean currency behavior) */
        $amount = round($amount);

        $operation = 'transaccion/traer';
        $url = $this->gatewayUrl . 'transaccion/' . $token;

        $this->_getLogHelper()->logDebug('Using gateway url: ' . $url);

        //Get headers
        $amount = $this->_formatAmount($amount);
        $message = $operation."\n".$token."\n".$trxId."\n".$amount;
        $headers = $this->getHeaders($message);

        $this->_getLogHelper()->logDebug('Using headers: ' . json_encode($headers));

        //Send request to puntopagos
        $result = $this->sendRequest($url, Zend_Http_Client::GET, $headers);

        $this->_getLogHelper()->logDebug('Loaded result: ' . $result, $trxId);

        $result = json_decode($result);
        return $result;
    }

    /**
     * Sign api message based on gateway rules
     *
     * @param $message
     * @return string
     */
    private function signMessage($message) {
        $signature = base64_encode(hash_hmac('sha1', $message, $this->keySecret, true));
        return "PP " . $this->keyId . ":" . $signature;
    }

    /**
     * Build request headers based on gateway format
     *
     * @param $message string message to be sent
     * @return array
     */
    private function getHeaders($message) {
        $date = gmdate("D, d M Y H:i:s", time())." GMT";
        $message .= "\n".$date;

        $headers = array(
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'Fecha' => $date,
            'Autorizacion' => $this->signMessage($message)
        );

        return $headers;
    }

    /**
     * Sent new request to the the api
     *
     * @param $url          string web service url
     * @param $method       string http method
     * @param $headers      array headers
     * @param $data         string request body
     * @return string
     */
    private function sendRequest($url, $method, $headers, $data = null) {
        try {
            $client = new Varien_Http_Client();
            $client->setUri($url)
                ->setMethod($method)
                ->setConfig(array(
                    'maxredirects' => 0,
                    'timeout' => 30,
                ));

            $client->setHeaders($headers);
            if (isset($data)) {
                $client->setRawData($data);
            }

            return $client->request()->getBody();
        } catch (HttpException $ex) {
            $this->_handleException($ex);
        } catch (Exception $e) {
            $this->_handleException($e);
        }
    }

    /**
     * Retrieve model helper
     *
     * @return Androb_Puntopagos_Helper_Data
     */
    private function _getHelper() {
        return Mage::helper('puntopagos');
    }

    /**
     * Format amount for api calls
     *
     * @param $amount
     * @return string
     */
    private function _formatAmount($amount) {
        return number_format($amount, 2, '.', '');
    }

    /**
     * @param Exception $ex
     * @throws Exception
     */
    public function _handleException($ex) {
        $this->_getLogHelper()->logDebug('Exception ocurred: ' . $ex->getMessage());
        throw $ex;
    }

    /**
     * Load log helper
     *
     * @return Androb_Puntopagos_Helper_Log
     */
    private function _getLogHelper() {
        return Mage::helper('puntopagos/log');
    }

}