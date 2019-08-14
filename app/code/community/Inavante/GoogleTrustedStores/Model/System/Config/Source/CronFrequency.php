<?php

class Inavante_GoogleTrustedStores_Model_System_Config_Source_CronFrequency {

    protected static $_options;

    public function toOptionArray() {
        
        $frequencyDaily   = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly  = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('cron')->__('Daily (Once in a day)'),
                    'value' => $frequencyDaily,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Weekly (Once in a week)'),
                    'value' => $frequencyWeekly,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Monthly (Once in a month)'),
                    'value' => $frequencyMonthly,
                ),
            );
        }
        return self::$_options;
    }

}
