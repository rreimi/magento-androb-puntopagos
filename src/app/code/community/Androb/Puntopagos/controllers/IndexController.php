<?php

class Androb_Puntopagos_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Show a success message for success transaction in puntopagos
     */
    public function successAction() {
        $token = $this->getRequest()->get('t');

        //You can do any operation here with the token if you want to show a custom invoice
        //Or create a custom success screen

        //If not token, redirect
        if (!$token) {
            $this->_redirect('checkout/cart');
        } else {
            $this->_redirect('checkout/onepage/success', array('_secure'=> true));
        }
    }

    /**
     * Show a failure messages, also is posible to restore cart based on backend configuration
     */
    public function failureAction() {
        /** @var Androb_Puntopagos_Model_Notification $notificationModel */

        $token = $this->getRequest()->get('t');


        //If not token, redirect
        //TODO make configurable restore cart after failure http://ka.lpe.sh/2011/12/31/magento-getting-back-shopping-cart-items-after-order-fails/

        if (!$token) {
            $this->_redirect('checkout/cart');
        } else {
            //TODO make configurable process failure transaction on failure request
            $notificationModel = Mage::getModel('puntopagos/notification');
            $notificationModel->processTransaction($token);

            $this->_redirect('checkout/onepage/failure', array('_secure'=> true));
        }
    }

    /**
     * Handle punto pagos payment notifications
     */
    public function notificationAction() {
        /** @var Androb_Puntopagos_Model_Notification $notificationModel */
        //TODO add heavy request validations here??

        $response = new stdClass();
        $token = $this->getRequest()->get('t');

        //If not token, redirect
        if (!$token) {
            $this->_redirect('checkout/cart');
        }

        $notificationModel = Mage::getModel('puntopagos/notification');
        $result = $notificationModel->processTransaction($token);

        //Prepare response to gateway
        $response->token = $token;
        $response->respuesta = $result->status; //99 or 00
        if (isset($result->message)) {
            $response->error = $result->message; // Error message
        }

        //Send response in json format
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
    }



    public function testAction() {
        //TODO Delete this method
        /** @var Androb_Puntopagos_Model_Api $api */
        /** @var Mage_Sales_Model_Order $order */

        $trxId = '100000063';

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($trxId);

        $customerName = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $orderItems = $order->getAllItems();

        /** @var Disenia_Giftcard_Model_Giftcard $giftCardModel */
        $giftCardModel = Mage::getModel('disenia_giftcard/giftcard');

        foreach ($orderItems as $item) {
            if ($item->getProductType() !=  'configurable') {
                continue;
            }

            //TODO verificar flag de gift card

            $giftcard = new stdClass;
            $giftcard->to = '';
            $giftcard->message = '';
            $giftcard->amount = $item->getBasePriceInclTax();
            $giftcard->from = $customerName;


            /** @var Mage_Sales_Model_Order_Item $item */
            //var_dump($item->getProductType());

            //var_dump($item->getProduct()->getDefaultAttributeSetId());
            //var_dump($item->getData());
            $options = $item->getProductOptions();

            //var_dump($options['options']);
            $optionModel = Mage::getModel('catalog/product_option');

            foreach ($options['options'] as $option) {
                if (isset($option['option_id'])) {
                    $optionModel->load($option['option_id']);

                    if ($optionModel->getData('code') === 'giftcard_to'){
                        $giftcard->to = $option['value'];
                    }

                    if ($optionModel->getData('code') === 'giftcard_message'){
                        $giftcard->message = $option['value'];
                    }
                }
            }

            //TODO VALIDATE GIFTCARD EMAIL
            $qty = $item->getQtyOrdered();

            for  ($i = 0; $i < $qty; $i++ ) {
                $giftcard->code = $giftCardModel->generateGiftCardCode($giftcard->amount);

                //TODO Make an object
                $giftCardModel->sendGiftCard($giftcard);
            }
        }
    }
}


