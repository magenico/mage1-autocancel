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
class Magenico_AutoCancel_Helper_Log extends Mage_Core_Helper_Abstract
{

    /**
     * XML configuration and filename for logging
     */
    private static $xml_path_logging_enabled = "magenico_autocancel/general/debug_enabled";
    private static $filename = 'magenico_autocancel.log';

    /**
     * boolean used to determine whether logging should be enabled
     * @var boolean
     */
    private $debug;

    /**
     * constructor, initializes the $debug property
     */
    public function __construct()
    {
        $this->debug = (bool) Mage::getStoreConfig(self::$xml_path_logging_enabled);
    }

    /**
     * logs messages with an optional user provided prefix to the log with filename specified above
     * in case of no user provided prefix, the system generates it's own
     * @param string message
     * @param string prefix
     */
    public function log($message, $prefix = null)
    {
        if ($this->isEnabled()) {
            if (!is_string($message)) {
                $message = print_r($message, true);
            }
            if (!$prefix) {
                $prefix = $this->getLogPrefix();
            }
            Mage::log($prefix . $message, null, $this->getLogFilename());
        }
    }

    /**
     * used to determine whether logging should be enabled, additional logic may be inserted if needed
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->debug;
    }

    /**
     * returns $filename which represents the filename of the log, can be changed
     * @return string
     */
    protected function getLogFilename()
    {
        return self::$filename;
    }

    /**
     * system generated prefix for a log entry in case user didn't provide one
     * @return string
     */
    private function getLogPrefix()
    {
        return ucfirst(Mage::app()->getRequest()->getActionName()) . ': ';
    }
}
