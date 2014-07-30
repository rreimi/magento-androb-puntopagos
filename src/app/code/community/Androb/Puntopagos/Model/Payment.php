<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:32 PM
 * 
 * PuntoPagos payment method implementation for magento
 */

abstract class Androb_Puntopagos_Model_Payment extends Mage_Payment_Model_Method_Abstract {

    //Disable payment method in admin/order pages.
    protected $_canUseInternal          = false;

    //Disable multi-shipping for this payment module.
    protected $_canUseForMultishipping  = false;

    //Disable recurring profiles
    protected $_canManageRecurringProfiles = false;

    //Declare initialize
    protected $_isInitializeNeeded = true;


    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    /** Override to use another value */
    public function isAvailable($quote = null) {

        if (!$this->_getHelper()->isGatewayActive()) {
            $this->_getLogHelper()->logDebug('Punto pagos is not active, check backend configuration');
            return false;
        }

        if (!$this->_getHelper()->isPaymentMethodActive($this->_code)) {
            $this->_getLogHelper()->logDebug('Current gateway is not active: ' . $this->_code . ', check backend configuration');
            return false;
        }

        return parent::isAvailable();
    }

    public function initialize($paymentAction, $stateObject) {
        $this->_getLogHelper()->logDebug('Calling: ' .  __METHOD__);
        $this->_getLogHelper()->logDebug('Current payment code: ' . $this->_code);

        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);

        try{
            /* Iniciar transaction en punto pago */
            $this->_initGateway();
        }catch (Exception $e){
            $this->_getLogHelper()->logDebug('Exception calling: ' .  __METHOD__);
            $this->_getLogHelper()->logDebug('Error detail is: ' . $e->getMessage());
            Mage::throwException($e->getMessage());
        }

        //Mage::throwException('could not init gateway');
        return $this;
    }

    /**
     * Create puntopagos transaction
     * Register in database the gateway generated transaction
     */
    private function _initGateway() {
        $this->_getLogHelper()->logDebug('Calling: ' .  __METHOD__);

        /** @var Mage_Checkout_Model_Session $sesionCheckout */
        $sesionCheckout = $this->getCheckout();

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getCheckout()->getQuote();

        //Get transaction information
        $trxId = $quote->getReservedOrderId();

        $this->_getLogHelper()->logDebug('Init puntopagos for orderId: ' . $trxId, $trxId);

        $amount = $quote->getGrandTotal();
        $paymentOption = $this->getInfoInstance()->getAdditionalInformation('payment_option');

        $this->_getLogHelper()->logDebug('Selected payment option is: ' . $paymentOption, $trxId);

        /** @var Androb_Puntopagos_Model_Api $api */
        $api = Mage::getModel('puntopagos/api');

        //Call de api to get transaction
        $result = $api->createTransaction($trxId, $amount, $paymentOption);

        //Persist result
        /** @var Androb_Puntopagos_Model_Transaction $transaction */
        $transaction = Mage::getModel('puntopagos/transaction');

        //TODO verificar si vale la pena persistir en caso de falla
        $transaction->setData('token', $result->token);
        $transaction->setData('trx_id', $trxId);
        $transaction->setData('status', $result->respuesta);
        $transaction->setData('amount', $amount);
        $transaction->setData('payment_option', $paymentOption);
        $transaction->setData('start_date', time());

        if (isset($result->detalle)) {
            //TODO Persist detail
        }

        $transaction->save();

        if ($result->respuesta == '00') {
            //Not necesary since quote gets inactive after order is placed
            //TODO should I remove quote id?
            $this->getCheckout()->setData('pp_quote_id', $sesionCheckout->getQuoteId());
            $this->getCheckout()->setData('pp_order_id', $trxId);
            $this->getCheckout()->setData('pp_token', $result->token);
            return true;
        } else {
            $this->_getLogHelper()->logDebug('Could not init transaction due to: ' . $result->error , $trxId);
            Mage::throwException('Could not init puntopagos transactions');
        }
    }

    /**
     * Overrided to asign payment type to additional information
     *
     * @param mixed $data
     * @return Mage_Payment_Model_Info
     */
    public function assignData($data) {

        $this->_getLogHelper()->logDebug('Calling: ' . __METHOD__);

        $result = parent::assignData($data);
        $info = $this->getInfoInstance();

        if (intval($data->getPaymentOption()) > 0) {
            $info->setAdditionalInformation('payment_option', $data->getPaymentOption());
        }

        /* User medio pago selection (ripley, presto, etc) */
        $this->_getLogHelper()->logDebug('Set method aditional to information: ' . $info->getAdditionalInformation());
        return $result;
    }


    public function getOrderPlaceRedirectUrl() {
        $token = $this->getCheckout()->getData('pp_token');
        $orderId = $this->getCheckout()->getData('pp_order_id');
        $this->_getLogHelper()->logDebug('Generating redirect url to token: ' . $token, $orderId);
        $url = $this->_getHelper()->getGatewayUrl() . 'transaccion/procesar/' . $token;
        $this->_getLogHelper()->logDebug('Url is: ' . $url);
        return $url;
    }

    /**
     * @return Androb_Puntopagos_Helper_Log
     */
    private function _getLogHelper() {
        return Mage::helper('puntopagos/log');
    }

    /**
     * Retrieve model helper
     *
     * @return Androb_Puntopagos_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('puntopagos');
    }

}