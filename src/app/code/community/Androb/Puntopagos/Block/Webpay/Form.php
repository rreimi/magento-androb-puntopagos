<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/3/14
 * Time: 1:13 AM
 *
 *
 */

class Androb_Puntopagos_Block_Webpay_Form extends Androb_Puntopagos_Block_PaymentForm {

    protected $_optionSourceModel = 'method_webpay_source_paymentOption';

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('puntopagos/webpay/form.phtml');
    }

}