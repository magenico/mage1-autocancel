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
$installer = $this;
$installer->startSetup();

$status = Mage::getModel('sales/order_status');
$helper = Mage::helper('magenico_autocancel');
$logger = Mage::helper('magenico_autocancel/log');
$label = $helper->getAutocancelStatusLabel();
$code = $helper->getAutocancelStatusCode();
$state = Mage_Sales_Model_Order::STATE_CANCELED;

$_stat = Mage::getModel('sales/order_status')->load($code);

if ($_stat->getStatus()) {
    $logger->log("[EVENT] autocancel status allready exists;");
    $installer->endSetup();
    return;
}
try {
    $status->setStatus($code)->setLabel($label)
        ->assignState($state)
        ->save();
    $logger->log("[EVENT] Added autocancel status;");
} catch (Exception $e) {
    $logger->log("[ERROR] creating magenico autocancel status:" . $e->getMessage());
}
$installer->endSetup();
