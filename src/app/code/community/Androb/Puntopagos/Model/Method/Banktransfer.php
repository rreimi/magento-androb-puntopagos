<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:32 PM
 * 
 * PuntoPagos payment method implementation for magento
 */

class Androb_Puntopagos_Model_Method_Banktransfer extends Androb_Puntopagos_Model_Payment {

    protected $_code  = 'pp_bank_transfer';
    protected $_formBlockType = 'puntopagos/banktransfer_form';

    public function __construct() {

    }
}