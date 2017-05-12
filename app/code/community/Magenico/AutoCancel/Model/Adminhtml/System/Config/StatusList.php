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
class Magenico_AutoCancel_Model_Adminhtml_System_Config_StatusList extends Mage_Core_Block_Html_Select
{

    /**
     * Returns status list array
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('magenico_autocancel');
        $stateCode = Mage_Sales_Model_Order::STATE_NEW;

        $statuses = Mage::getResourceModel('sales/order_status_collection')
            ->addStateFilter($stateCode)
            ->toOptionHash();

        $result = array();
        foreach ($statuses as $status_code => $status_label) {
            $result[] = array(
                'value' => $status_code,
                'label' => $helper->__($status_label),
            );
        }
        return $result;
    }

    /**
     * Returns status select input html
     * @return string
     */
    public function _toHtml()
    {
        $options = $this->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }
        return parent::_toHtml();
    }

    /**
     * Sets the status select input html
     * @param string $value
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
