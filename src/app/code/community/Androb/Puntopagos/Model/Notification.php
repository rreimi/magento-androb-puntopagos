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

class Androb_Puntopagos_Model_Notification {

    const RESULT_STATUS_OK = '00';
    const RESULT_STATUS_ERROR = '99';

    /**
     * Process incoming transactions
     * Update magento order accordingly
     *
     * @param $token        puntopagos transaction token
     * @return stdClass
     */
    public function processTransaction($token) {

        $this->_getLogHelper()->logDebug('Calling ' . __METHOD__);

        $result = new stdClass;

        //Load transaction
        $transaction  = Mage::getModel('puntopagos/transaction');
        $transaction->load($token, 'token');

        if ($transaction->getId()){

            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order');
            $orderId = $transaction->getData('trx_id');
            $order->loadByIncrementId($orderId);

            $this->_getLogHelper()->logDebug('Processing transaction token: ' . $token . ', for order: ' . $orderId, $orderId);

            //If order has not been processed
            if ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {

                /** @var Androb_Puntopagos_Model_Api $api */
                $api = Mage::getModel('puntopagos/api');
                $trxId = $transaction->getData('trx_id');
                $amount = $transaction->getData('amount');
                $apiResponse = $api->getTransaction($token, $trxId, $amount);
                $transaction = $this->updateTransaction($transaction, $apiResponse);

                //Process order based on status
                if ($transaction->getData('status') == '00') {
                    $this->_getLogHelper()->logDebug('Success transaction for token ' . $token . ', updating order: ' . $orderId, $orderId);

                    //TODO Important validar montos de la operacion vs montos de la orden?
                    //TODO Important validar id de la orden??

                    //if ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) { not necessary
                        //todo create invoice
                        $msg = 'Payment completed using puntopagos (' . $transaction->getData('payment_option_desc') . ')';
                        $order->addStatusHistoryComment($transaction->getData('response'), Mage_Sales_Model_Order::STATE_CANCELED);

                        $order->sendNewOrderEmail();
                        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg, false);
                        $order->setStatus($this->_getHelper()->getGatewayOrderStatus());
                        $order->save();

                        $this->_getLogHelper()->logDebug('Saving order: ' . $orderId, $orderId);

                        //Create invoice for order
                        if ($this->_getHelper()->isCreateInvoiceActive()) {
                            $this->createInvoice($order);
                        }
                   // }
                } else {
                    $this->_getLogHelper()->logDebug('Failed transaction for token ' . $token . ', updating order: ' . $orderId, $orderId);

                    if ($order->canCancel()) {
                        $this->_getLogHelper()->logDebug('Canceling order ' . $orderId, $orderId);

                        //TODO Important custom status here base on transaction status?? i

                        //Cancel order
                        $order->cancel();
                        $order->addStatusHistoryComment($transaction->getData('response'), Mage_Sales_Model_Order::STATE_CANCELED);
                        //$order->sendOrderUpdateEmail();
                        $order->save();

                    } else {
                        $this->_getLogHelper()->logDebug('Order: ' . $orderId . ' already canceled', $orderId);
                    }
                }

                $result->status = self::RESULT_STATUS_OK;
                $result->message = 'order_processed';

            } else {
                $this->_getLogHelper()->logDebug('Order ' . $orderId . ' has been processed before', $orderId);
                $result->status = self::RESULT_STATUS_OK;
                $result->message = 'already_processed';
            }

        } else {
            $this->_getLogHelper()->logDebug('Transaction not found for token ' . $token);
            $result->status = self::RESULT_STATUS_ERROR;
            $result->message = 'token_not_found';
        }

        return $result;
    }

    /**
     * Create invoice for given order
     *
     * @param Mage_Sales_Model_Order $order
     */
    private function createInvoice($order) {
        $this->_getLogHelper()->logDebug('Calling ' . __METHOD__);
        try {

            if(!$order->canInvoice()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }

            /** @var Mage_Sales_Model_Order_Invoice $invoice */
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            if (!$invoice->getTotalQty()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
            }

            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();

            $this->_getLogHelper()->logDebug('Invoice created sucessfully', $order->getIncrementId());

        } catch (Mage_Core_Exception $e) {
            $this->_getLogHelper()->logDebug($e->getMessage(), $order->getIncrementId());
        }
    }

    /**
     * Update transaction with using response object information
     *
     * @param $transaction
     * @param $apiResponse
     * @return mixed
     */
    private function updateTransaction($transaction, $apiResponse) {

        $this->_getLogHelper()->logDebug('Calling ' . __METHOD__);

        //Update transaction
        $transaction->setData('status', $apiResponse->respuesta);

        if (isset($apiResponse->fecha_aprobacion)) {
            $transaction->setData('approval_date', $apiResponse->fecha_aprobacion);
        }

        if (isset($apiResponse->numero_operacion)) {
            $transaction->setData('operation_number', $apiResponse->numero_operacion);
        }

        if (isset($apiResponse->codigo_autorizacion)) {
            $transaction->setData('auth_code', $apiResponse->codigo_autorizacion);
        }

        if (!empty($apiResponse->error)){
            $transaction->setData('response', $apiResponse->error);
        }

        if (!empty($apiResponse->medio_pago)){
            $transaction->setData('payment_option', $apiResponse->medio_pago);
        }

        if (!empty($apiResponse->medio_pago_descripcion)){
            $transaction->setData('payment_option_desc', $apiResponse->medio_pago_descripcion);
        }

        $transaction->save();
        return $transaction;
    }

    /**
     * @return Androb_Puntopagos_Helper_Log
     */
    private function _getLogHelper() {
        return Mage::helper('puntopagos/log');
    }

    /**
     * @return Androb_Puntopagos_Helper_Data
     */
    private function _getHelper() {
        return Mage::helper('puntopagos');
    }
}