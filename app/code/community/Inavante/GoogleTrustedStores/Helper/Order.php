<?php

class Inavante_GoogleTrustedStores_Helper_Order extends Mage_Core_Helper_Abstract {

    public function getGtsCanceledOrder($store) {

        $type = Mage_Sales_Model_Order::STATE_CANCELED;
        return $this->getGtsOrder($store, $type);
    }

    public function getGtsShipOrder($store) {

        $type = Mage_Sales_Model_Order::STATE_COMPLETE;
        return $this->getGtsOrder($store, $type);
    }

    public function getGtsRmaOrder($store) {

        if ($table = mage::helper('googletrustedstores')->isRmaEE()) {

            $gtsFlag = array('neq' =>
                array(Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Exported_RMA));

            //Join
            $aliasTable = array('rma' => $table);
            $aliasField = array('rma_status' => 'rma.status');

            $conditions = join(' AND ', array(
                'rma.order_increment_id=main_table.increment_id',
                'rma.status=\'authorized\'')
            );

            $collection = $this->getGtsOrder($store, null, $gtsFlag);
            $rmaSelect = $collection->getSelect()
                    ->join($aliasTable, $conditions, $aliasField);

            return $collection;
        }
    }

    public function getGtsOrder($store = null, $type = null, $gtsExport = null, $days = -7) {

        if ($gtsExport == null) {
            $gtsExport = Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection::Not_Exported;
        }

        $collection = Mage::getResourceModel('googletrustedstores/order_collection');

        $collection
                ->addGtsExportedFilter($gtsExport)
                ->addAttributeToSelect('*')
                ->addDaysFilter($days);

        if ($type != null) {
            $collection = $collection->addTypeFilter($type);
        }
        if ($store != null) {
            $collection = $collection->addStoreFilter($store);
        }

        return $collection;
    }

    public function rmaJoinCleanItem($collection) {

        foreach ($collection as $key => $order) {
            if ($order->getRmaStatus() != 'authorized' && $order->getState() != 'canceled') {
                $collection->removeItemByKey($key);
            }
        }

        return $collection;
    }

}