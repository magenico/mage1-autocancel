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
class Magenico_AutoCancel_Block_Adminhtml_System_Config_PaymentSpecificCancel extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_intervalRenderer;
    protected $_paymentRenderer;

    public function _prepareToRender()
    {
        $helper = Mage::helper('magenico_autocancel/render');
        if (!$this->_intervalRenderer) {
            $this->_intervalRenderer = $helper->getIntervalTypeRender($this);
        }
        if (!$this->_paymentRenderer) {
            $this->_paymentRenderer = $helper->getPaymentsListRender($this);
        }
        $this->addColumn('payment_method', array(
            'label' => $helper->__('Payment Method'),
            'style' => 'width:300px',
            'class' => 'validate-select',
            'renderer' => $this->_paymentRenderer,
        ));
        $this->addColumn('interval_type', array(
            'label' => $helper->__('Interval type'),
            'style' => 'width:50px',
            'class' => 'validate-select',
            'renderer' => $this->_intervalRenderer,
        ));
        $this->addColumn('interval_amount', array(
            'label' => $helper->__('Interval amount'),
            'class' => 'required-entry validate-greater-than-zero',
            'style' => 'width:50px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = $helper->__('Add');
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $helper = Mage::helper('magenico_autocancel/render');
        if (!$this->_intervalRenderer) {
            $this->_intervalRenderer = $helper->getIntervalTypeRender($this);
        }
        if (!$this->_paymentRenderer) {
            $this->_paymentRenderer = $helper->getPaymentsListRender($this);
        }
        $row->setData(
            'option_extra_attr_' . $this->_intervalRenderer->calcOptionHash($row->getData('interval_type')), 'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_paymentRenderer->calcOptionHash($row->getData('payment_method')), 'selected="selected"'
        );
    }
}
