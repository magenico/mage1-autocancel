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
class Magenico_AutoCancel_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_CONFIG_ENABLED = 'magenico_autocancel/general/enabled';
    const XML_CONFIG_DEBUG_ENABLED = 'magenico_autocancel/general/debug_enabled';
    const XML_CONFIG_GLOBAL_STATUSES = 'magenico_autocancel/global/statuses';
    const XML_CONFIG_GLOBAL_INTERVAL_TYPE = 'magenico_autocancel/global/interval_type';
    const XML_CONFIG_GLOBAL_INTERVAL_AMOUNT = 'magenico_autocancel/global/interval_amount';
    const XML_CONFIG_GLOBAL_START_DATE = 'magenico_autocancel/global/start_date';
    const XML_CONFIG_PAYMENT_SPECIFIC_RULES_DATA = 'magenico_autocancel/payment_specific/rules_data';
    const AUTOCANCEL_STATUS_CODE = 'magenico_autocanceled';
    const AUTOCANCEL_STATUS_LABEL = 'Auto-canceled';
    const CRON_ORDER_LIMIT = 100;

    /**
     * 
     * @return integer
     */
    public function getCronOrderLimit()
    {
        return self::CRON_ORDER_LIMIT;
    }

    /**
     * 
     * @return string
     */
    public function getCancelStatus()
    {
        $code = $this->getAutocancelStatusCode();
        $_stat = Mage::getModel('sales/order_status')->load($code);
        if ($_stat->getStatus()) {
            return $code;
        }
        return 'canceled';
    }

    /**
     * Returns autocancel status code
     * @return type
     */
    public function getAutocancelStatusCode()
    {
        return self::AUTOCANCEL_STATUS_CODE;
    }

    /**
     * Returns autocancel status label
     * @return type
     */
    public function getAutocancelStatusLabel()
    {
        return self::AUTOCANCEL_STATUS_LABEL;
    }

    /**
     * Returns payment specific rules
     * @param integer $websiteId
     * @return array
     */
    public function getPaymentSpecificRules($websiteId = null)
    {
        return unserialize($this->getWebsiteConfig(self::XML_CONFIG_PAYMENT_SPECIFIC_RULES_DATA, $websiteId));
    }

    /**
     * Returns interval in a string format
     * @param integer $websiteId
     * @return string
     */
    public function getInterval($websiteId = null)
    {
        $type = $this->getIntervalType($websiteId);
        $amount = $this->getIntervalAmount($websiteId);
        if ($amount <= 0) {
            return "";
        }
        return (trim($amount) . " " . trim($type));
    }

    /**
     * Returns whether the module is enabled
     * @param integer $websiteId
     * @return bool
     */
    public function isEnabled($websiteId = null)
    {
        return ($this->getWebsiteConfig(self::XML_CONFIG_ENABLED, $websiteId) == 1);
    }

    /**
     * 
     * @param integer $websiteId
     * @return string
     */
    public function getStartDate($websiteId = null)
    {
        $date = $this->getWebsiteConfig(self::XML_CONFIG_GLOBAL_START_DATE, $websiteId);
        if (strlen($date) <= 1) {
            $date = date("Y-m-d 00:00:00");
            $config = new Mage_Core_Model_Config();
            $config->saveConfig(self::XML_CONFIG_GLOBAL_START_DATE, $date);
            return $date;
        }
        return date("Y-m-d H:i:s", strtotime($date));
    }

    /**
     * 
     * @param integer $websiteId
     * @return type
     */
    public function getGlobalStatuses($websiteId = null)
    {
        return $this->getWebsiteConfig(self::XML_CONFIG_GLOBAL_STATUSES, $websiteId);
    }

    /**
     * Returns interval type in string format
     * @param integer $websiteId
     * @return string
     */
    public function getIntervalType($websiteId = null)
    {
        return $this->getWebsiteConfig(self::XML_CONFIG_GLOBAL_INTERVAL_TYPE, $websiteId);
    }

    /**
     * Returns interval amount
     * @param integer $websiteId
     * @return float
     */
    public function getIntervalAmount($websiteId = null)
    {
        return $this->getWebsiteConfig(self::XML_CONFIG_GLOBAL_INTERVAL_AMOUNT, $websiteId);
    }

    /**
     * Logs messages if debug mode is enabled
     * @param string $message
     * @param string $prefix
     * @param integer $websiteId
     */
    public function log($message, $prefix, $websiteId = null)
    {
        $shouldLog = ($this->getWebsiteConfig(self::XML_CONFIG_DEBUG_ENABLED, $websiteId) == 1);
        if ($shouldLog > 0) {
            Mage::helper('magenico_autocancel/log')->log($message, $prefix);
        }
    }

    /**
     * Returns magento website configuration for specified website id
     * @param string $path
     * @param integer $websiteId
     * @return string
     */
    public function getWebsiteConfig($path, $websiteId = null)
    {
        if ($websiteId === null) {
            return Mage::app()->getWebsite()->getConfig($path);
        }
        return Mage::app()->getWebsite($websiteId)->getConfig($path);
    }
}
