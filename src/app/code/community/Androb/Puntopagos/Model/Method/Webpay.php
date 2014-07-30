<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:32 PM
 * 
 * Puntopagos payment method implementation for magento
 */

class Androb_Puntopagos_Model_Method_Webpay extends Androb_Puntopagos_Model_Payment {

    protected $_code  = 'webpay';
    protected $_formBlockType = 'puntopagos/webpay_form';

    public function __construct() {

    }
}