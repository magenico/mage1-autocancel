<?php

/**
 * Magenico DOO Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenico DOO Module License
 * that is bundled with this package in the file license.pdf.
 * It is also available through the world-wide-web at this URL:
 * http://www.magenico.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@magenico.com so we can send you a copy immediately.
 *
 * @category   Magenico
 * @package    Magenico_AutoCancel
 * @copyright  Copyright (c) 2017 Magenico DOO
 * @license    http://www.magenico.com/license
 */
class Magenico_AutoCancel_Block_Adminhtml_System_Config_PaymentList extends Mage_Core_Block_Html_Select
{

    public function _toHtml()
    {
        $helper = Mage::helper('payment');
        $options = $helper->getPaymentMethodList(true, true, true);
        foreach ($options as $option) {
            $label = $option['label'] ? $option['label'] : $option['value'];
            $this->addOption($option['value'], $helper->__($label));
        }
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
