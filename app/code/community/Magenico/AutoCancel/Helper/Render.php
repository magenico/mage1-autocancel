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
class Magenico_AutoCancel_Helper_Render extends Mage_Core_Helper_Abstract
{

    /**
     * 
     * @param $block
     * @return Magenico_AutoCancel_Block_Adminhtml_System_Config_PaymentList
     */
    public function getPaymentsListRender($block)
    {
        return $block->getLayout()->createBlock(
                'magenico_autocancel/adminhtml_system_config_paymentList', '', array('is_render_to_js_template' => true)
        );
    }

    /**
     * 
     * @param $block
     * @return Magenico_AutoCancel_Block_Adminhtml_System_Config_IntervalTypeList
     */
    public function getIntervalTypeRender($block)
    {
        return $block->getLayout()->createBlock(
                'magenico_autocancel/adminhtml_system_config_intervalTypeList', '', array('is_render_to_js_template' => true)
        );
    }
}
