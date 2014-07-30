<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:30 PM
 * 
 * Class to handle punto pagos rest api requests
 *
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

        $result = $this->sendRequest($url, HTTP_METH_POST, $headers, $data);

        $this->_getLogHelper()->logDebug('Loaded result: ' . $result, $trxId);

        $result = json_decode($result);
        return $result;
    }

    public function getTransaction($token, $trxId, $amount) {

        $this->_getLogHelper()->logDebug('Loading transaction for token: ' . $token, $trxId);
        $this->_getLogHelper()->logDebug('Using trxId (orderId): ' . $trxId . ', amount: ' . $amount);

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
        $result = $this->sendRequest($url, HTTP_METH_GET, $headers);

        $this->_getLogHelper()->logDebug('Loaded result: ' . $result, $trxId);

        $result = json_decode($result);
        return $result;
    }

    private function signMessage($message) {
        $signature = base64_encode(hash_hmac('sha1', $message, $this->keySecret, true));
        return "PP " . $this->keyId . ":" . $signature;
    }

    private function getHeaders($message) {
        $date = gmdate("D, d M Y H:i:s", time())." GMT";
        $message .= "\n".$date;

        $headers = array(
            'Accept' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'Fecha' => $date,
            'Autorizacion' => $this->signMessage($message)
        );

        return $headers;
    }

    private function sendRequest($url, $method, $headers, $data = null) {
        try {
            $ssl_array = array('version' => HttpRequest::SSL_VERSION_SSLv3);
            $options = array('headers' => $headers,
                'protocol' => HTTP_VERSION_1_1,
                'ssl' => $ssl_array);

            $request = new HttpRequest($url, $method, $options);
            $request->setContentType("application/json; charset=utf-8");
            if (isset($data)) {
                $request->setRawPostData($data);
            }
            return $request->send()->getBody();
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
     * @return Androb_Puntopagos_Helper_Log
     */
    private function _getLogHelper() {
        return Mage::helper('puntopagos/log');
    }

}