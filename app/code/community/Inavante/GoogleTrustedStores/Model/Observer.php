<?php

class Inavante_GoogleTrustedStores_Model_Observer {

    public function setGoogleJsOnOrderSuccessPageView(Varien_Event_Observer $observer) {
        if (!Mage::getStoreConfigFlag('googletrustedstores/config/enabled', Mage::app()->getStore()->getId())) {
            return $this;
        }
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('googletrustedstores.js');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
        return $this;
    }

    public function generateShipmentFeedCanceled() {

        $i = 0;
        $orderHelper = Mage::helper('googletrustedstores/order');
        $helper = Mage::helper('googletrustedstores');

        foreach (Mage::app()->getStores() as $store) {

            $enabled = Mage::getStoreConfigFlag('googletrustedstores/config/enabled', $store->getId());

            if (!$enabled) {
                continue;
            }

            $collection = $orderHelper->getGtsCanceledOrder($store);
            if (Mage::helper('googletrustedstores')->isRmaEE())
                $rma = $orderHelper->getGtsRmaOrder($store);

            if (count($collection) || (($rma) && count($rma))) {

                $fileName = Mage::getStoreConfig('googletrustedstores/config/feed_filename_canceled', $store->getId());
                $delimiter = Mage::getStoreConfig('googletrustedstores/config/feed_delimiter', $store->getId());
                $path = Mage::getBaseDir('media') . DS . $fileName;

                if (!file_exists($path)) {
                    // FORMAT: [Order_id], [Reason]
                    file_put_contents($path, implode(
                                    $delimiter, array('merchant order id', 'reason')) . "\n", FILE_APPEND | LOCK_EX
                    );
                }

                foreach ($collection as $order) {

                    $reason = 'MerchantCanceled';

                    // FORMAT: [Order_id], [Reason]
                    file_put_contents($path, implode($delimiter, array($order->getIncrementId(), $reason)) . "\n", FILE_APPEND | LOCK_EX);

                    $order->setGtsExported(Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Exported);
                    $order->save();

                    $helper->setCanceledLog($order->getIncrementId(), $reason, Mage::getModel('core/date')->gmtDate());

                    $i++;
                }

                if (Mage::helper('googletrustedstores')->isRmaEE()) {
                    foreach ($rma as $order) {

                        if ($order->getRmaStatus() == 'authorized') {

                            $reason = 'canceled by customer';
                            // FORMAT: [Order_id], [Reason]
                            file_put_contents($path, implode($delimiter, array($order->getIncrementId(), $reason)) . "\n", FILE_APPEND | LOCK_EX);

                            $order->setGtsExported(Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Exported_RMA);
                            $order->save();

                            $helper->setCanceledLog($order->getIncrementId(), $reason, Mage::getModel('core/date')->gmtDate());

                            $i++;
                        }
                    }
                }

                if (Mage::getStoreConfigFlag('googletrustedstores/config/feed_ftp_upload', $store->getId())) {
                    $this->_ftpUpload(
                            $fileName, Mage::getStoreConfig('googletrustedstores/config/feed_ftp_server', $store->getId()), Mage::getStoreConfig('googletrustedstores/config/feed_ftp_username', $store->getId()), Mage::getStoreConfig('googletrustedstores/config/feed_ftp_password', $store->getId()), true
                    );
                }
            }
        }
        return $i;
    }

    public function generateShipmentFeedShipped() {

        $i = 0;
        $orderHelper = Mage::helper('googletrustedstores/order');
        $helper = Mage::helper('googletrustedstores');

        foreach (Mage::app()->getStores() as $store) {

            $enabled = Mage::getStoreConfigFlag('googletrustedstores/config/enabled', $store->getId());
            if (!$enabled) {
                continue;
            }

            $collection = $orderHelper->getGtsShipOrder($store);

            if (count($collection)) {

                $fileName = Mage::getStoreConfig('googletrustedstores/config/feed_filename_shipped', $store->getId());
                $delimiter = Mage::getStoreConfig('googletrustedstores/config/feed_delimiter', $store->getId());
                $path = Mage::getBaseDir('media') . DS . $fileName;

                if (!file_exists($path)) {
                    file_put_contents($path, implode(
                                    $delimiter, array('merchant order id', 'tracking number', 'carrier code', 'other carrier name', 'ship date')) . "\n", FILE_APPEND | LOCK_EX
                    );
                }

                foreach ($collection as $order) {

                    foreach ($order->getShipmentsCollection() as $shipment) {
                        // format:[Order_increment_id],[TrackNumber],[Title],[Other_Title], [Ship date]
                        $contents = Mage::helper('googletrustedstores/shipment')->GetTrackContent($shipment, $order->getIncrementId());
                    }

                    file_put_contents($path, implode($delimiter, $contents) . "\n", FILE_APPEND | LOCK_EX);

                    $order->setGtsExported(Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Exported)
                            ->save();

                    $trackNumber = $contents[1];
                    $shipmentTitle = ($contents[3] == '') ? $contents[2] : $contents[3];

                    $helper->setShipLog(
                            $order->getIncrementId(), $trackNumber, $shipmentTitle, Mage::getModel('core/date')->gmtDate()
                    );

                    $i++;
                }

                if (Mage::getStoreConfigFlag('googletrustedstores/config/feed_ftp_upload', $store->getId())) {
                    $this->_ftpUpload(
                            $fileName, Mage::getStoreConfig('googletrustedstores/config/feed_ftp_server', $store->getId()), Mage::getStoreConfig('googletrustedstores/config/feed_ftp_username', $store->getId()), Mage::getStoreConfig('googletrustedstores/config/feed_ftp_password', $store->getId()), true
                    );
                }
            }
        }
        return $i;
    }

    protected function _ftpUpload($fileName, $ftpServer, $ftpUsername, $ftpPassword, $unlink = false) {

        if (!extension_loaded('ftp')) {
            throw new Inavante_GoogleTrustedStores_Exception(Mage::helper('googletrustedstores')->__('"ftp" extension is not available on this server.'));
        }

        $path = Mage::getBaseDir('media') . DS . $fileName;
        if (file_exists($path)) {
            $connId = ftp_connect($ftpServer);
            $loginResult = ftp_login($connId, $ftpUsername, $ftpPassword);
            if ($loginResult && ftp_put($connId, $fileName, $path, FTP_ASCII)) {
                if ($unlink) {
                    unlink($path);
                }
            } else {
                throw new Inavante_GoogleTrustedStores_Exception(Mage::helper('googletrustedstores')->__('There was a problem while uploading file: %s', $path));
            }
            ftp_close($connId);
        }
        return $this;
    }

}

