<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:32 PM
 * 
 * Puntopagos payment method implementation for magento
 */

class Androb_Puntopagos_Model_Method_Commercialcard extends Androb_Puntopagos_Model_Payment {

    protected $_code  = 'pp_commercial_card';
    protected $_formBlockType = 'puntopagos/commercialcard_form';

    public function __construct() {

    }
}