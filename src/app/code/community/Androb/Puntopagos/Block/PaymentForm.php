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

abstract class Androb_Puntopagos_Block_PaymentForm extends Mage_Payment_Block_Form {

    /** @var $optionSourceModel String - The model name used to fetch the payment options */
    protected $_optionSourceModel;

    /**
     * Get available payment options for specific payment method.
     * Options are returned based on environment configuration (sandbox / production)
     *
     * @param $code
     * @return array
     */
    public function getAvailablePaymentOptions($code) {
        /** @var Androb_Puntopagos_Helper_Data $helper */
        $helper = Mage::helper('puntopagos');

        $activeOptions = $helper->getActivePaymentOptions($code);

        $isSandboxMode = $helper->isSandboxMode();

        $sourceModel = Mage::getModel('puntopagos/' . $this->_optionSourceModel);
        $options = $sourceModel->toOptionArray();

        $result = array();

        $iconBaseUrl = $helper->getPaymentIconBaseUrl();

        foreach ($options as $option) {

            if ($isSandboxMode) {
                if ($option['sandbox'] != true) {
                    continue;
                }
            }

            if (in_array($option['value'], $activeOptions) ) {
                $option['icon_url'] = $iconBaseUrl . 'mp' . $option['value'] . '.gif';
                $result[] = $option;
            }
        }

        return $result;
    }
}