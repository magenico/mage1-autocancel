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
class Magenico_AutoCancel_Model_Observer
{

    /**
     * Returns Autocancel helper instance
     * @return Magenico_AutoCancel_Helper_Data
     */
    private function getHelper()
    {
        return Mage::helper('magenico_autocancel');
    }

    /**
     * Cron process for canceling pending orders
     */
    public function cancelPendingOrders()
    {
        $helper = $this->getHelper();
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
            $websiteId = $website->getId();
            if (!$helper->isEnabled($websiteId)) {
                //$helper->log("[$websiteId] module disabled;", "[EVENT] ", $websiteId);
                continue;
            }
            $storeIds = $website->getStoreIds();
            // GLOBAL RULE 
            $helper->log("[$websiteId] Applying global autocancel rule;", "[EVENT]", $websiteId);
            $this->applyGlobalRule($websiteId, $storeIds);

            // PAYMENT SPECIFIC RULES
            $prules = $helper->getPaymentSpecificRules($websiteId);
            if (count($prules) == 0) {
                continue;
            }
            $helper->log("[$websiteId] Applying specific autocancel rules;", "[EVENT]", $websiteId);
            foreach ($prules as $index => $_rule) {
                $this->applySpecificRule($websiteId, $storeIds, $_rule);
            }
        }
        return;
    }

    /**
     * 
     * @param integer $websiteId
     * @param array $storeIds
     */
    private function applyGlobalRule($websiteId, $storeIds)
    {
        $helper = $this->getHelper();
        $interval = $helper->getInterval($websiteId);
        $statuses = explode(',', $helper->getGlobalStatuses($websiteId));
        $start_date = $helper->getStartDate($websiteId);
        $limit = $helper->getCronOrderLimit();

        if (strlen($interval) <= 1) {
            $helper->log("Global rule disabled via interval; returning;", "[EVENT][$websiteId] ", $websiteId);
            return;
        }

        $orderCollection = Mage::getResourceModel('sales/order_collection');
        $orderCollection
            ->addFieldToFilter('status', array('in' => $statuses))
            ->addFieldToFilter('store_id', array('in' => $storeIds))
            ->addFieldToFilter('updated_at', array(
                'lt' => new Zend_Db_Expr("DATE_SUB('" . now() . "', INTERVAL " . $interval . ")")))
            ->addFieldToFilter('updated_at', array('gt' => "$start_date"))
            ->getSelect()
            ->limit($limit);

        $this->cancelOrders($orderCollection, $websiteId);
    }

    /**
     * 
     * @param type $websiteId
     * @param type $storeIds
     * @param type $rule
     */
    private function applySpecificRule($websiteId, $storeIds, $rule)
    {
        $helper = $this->getHelper();
        $interval_type = trim($rule['interval_type']);
        $interval_amount = trim($rule['interval_amount']);
        $start_date = $helper->getStartDate($websiteId);
        $limit = $helper->getCronOrderLimit();
        $payment_table = Mage::getResourceModel('sales/order_collection')->getTable('sales/order_payment');
        try {
            $orderCollection = Mage::getResourceModel('sales/order_collection');
            $orderCollection
                ->addFieldToFilter('payment.method', array('eq' => trim($rule['payment_method'])))
                ->addFieldToFilter('store_id', array('in' => $storeIds))
                ->addFieldToFilter('updated_at', array(
                    'lt' => new Zend_Db_Expr("DATE_SUB('" . now() . "', INTERVAL $interval_amount $interval_type)")))
                ->addFieldToFilter('updated_at', array('gt' => "$start_date"))
                ->getSelect()
                ->limit($limit)
                ->join(
                    array('payment' => $payment_table), 'main_table.entity_id=payment.parent_id', array('payment_method' => 'payment.method')
            );
        } catch (Exception $e) {
            $helper->log($e->getMessage(), "[ERROR] ", $websiteId);
        }
        $this->cancelOrders($orderCollection, $websiteId);
    }

    /**
     * Cancels all orders in passed order collection with autocancel status
     * @param $collection
     * @param integer $websiteId
     */
    private function cancelOrders($collection, $websiteId)
    {
        $helper = $this->getHelper();
        $cancelStatus = $helper->getCancelStatus();
        foreach ($collection->getItems() as $order) {
            $orderModel = Mage::getModel('sales/order');
            $orderModel->load($order['entity_id']);

            if (!$orderModel->canCancel()) {
                continue;
            }
            try {
                $orderModel->cancel();
            } catch (Exception $e) {
                $helper->log("[$websiteId] Exception canceling order with id:" . $order->increment_id, "[ERROR] ", $websiteId);
                $helper->log("[$websiteId] Exception message:" . $e->getMessage(), '[ERROR] ', $websiteId);
                continue;
            }
            $orderModel->setStatus($cancelStatus);
            $orderModel->save();
            $helper->log("[$websiteId] AutoCancel: order[" . $order->increment_id . "] canceled", "[EVENT] ", $websiteId);
        }
    }
}
