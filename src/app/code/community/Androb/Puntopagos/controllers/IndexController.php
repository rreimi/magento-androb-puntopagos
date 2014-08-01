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

}


