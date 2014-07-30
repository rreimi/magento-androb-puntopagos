<?php
/**
 * Created by Androb (www.androb.com).
 * User: rreimi
 * Date: 7/2/14
 * Time: 4:32 PM
 * 
 * Handle punto pagos transaction table
 */

class Androb_Puntopagos_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('puntopagos/transaction', 'id');
    }
}