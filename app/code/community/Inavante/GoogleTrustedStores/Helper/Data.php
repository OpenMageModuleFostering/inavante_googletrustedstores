<?php

class Inavante_GoogleTrustedStores_Helper_Data extends Mage_Core_Helper_Abstract {

    // Check if it is EE and RMA is avariable
    public function isRmaEE() {
//        $ver = (int) (substr(implode('', Mage::getVersionInfo()), 0, 3));
//        return ( Mage::getEdition() == Mage::EDITION_ENTERPRISE && $ver >= 111);

        try {
            $table = Mage::getSingleton('core/resource')->getTableName('enterprise_rma/rma');
        } catch (Exception $e) {
            return false;
        }

        return $table;
    }

    public function setCanceledLog($id, $reason, $date) {

        $model = Mage::getModel('googletrustedstores/log');

        $model
                ->setOrderId($id)
                ->setStatus(Mage_Sales_Model_Order::STATE_CANCELED)
                ->setCancelReason($reason)
                ->setCreatedAt($date);

        $model->save();
    }

    public function setShipLog($id, $trackNumber, $shipmentTitle, $date) {

        $model = Mage::getModel('googletrustedstores/log');

        $model
                ->setOrderId($id)
                ->setStatus('shipped')
                ->setShipmentTitle($shipmentTitle)
                ->setTrackNumber($trackNumber)
                ->setCreatedAt($date);

        $model->save();
    }

    /**
     * TEST METHOD
     * 
     * 
     */
    public function testCleanAll() {
        //CLEAN gts_export FLAG
        echo '- Clean Order Exported Flag' . '<br>';

        $gtsFlag = array('neq' => Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Not_Exported);
        $collection = Mage::helper('googletrustedstores/order')
                ->getGtsOrder(null, null, $gtsFlag);

        foreach ($collection as $order) {
            $order->setGtsExported(Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Not_Exported)
                    ->save();
        }

        //CLEAN log TABLE
        $collection = Mage::getModel('googletrustedstores/log')->getCollection();
        foreach ($collection as $log) {
            $log->delete();
        }
        echo '- Clean Log Table ' . '<br>';
        //REMOVE files
        $fileName = Mage::getStoreConfig('googletrustedstores/config/feed_filename_canceled');
        $path = Mage::getBaseDir('media') . DS . $fileName;
        if (file_exists($path)) {
            unlink($path);
            echo '- Delete File: ' . $fileName . '<br>';
        }

        $fileName = Mage::getStoreConfig('googletrustedstores/config/feed_filename_shipped');
        $path = Mage::getBaseDir('media') . DS . $fileName;
        if (file_exists($path)) {
            unlink($path);
            echo '- Delete File: ' . $fileName . '<br>';
        }
        return true;
    }

}