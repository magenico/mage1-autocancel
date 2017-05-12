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
class Magenico_AutoCancel_Block_Adminhtml_System_Config_IntervalTypeList extends Mage_Core_Block_Html_Select
{

    public function _toHtml()
    {
        $options = Mage::getSingleton('magenico_autocancel/adminhtml_system_config_intervalTypes')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
