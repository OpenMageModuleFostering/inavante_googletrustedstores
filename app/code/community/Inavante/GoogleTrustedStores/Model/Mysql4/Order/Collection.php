<?php

class Inavante_GoogleTrustedStores_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection {

    const Not_Exported = '0';
    const Exported = '1';
    const Exported_RMA = '2';

    public function addDaysFilter($days = -7) {
        return $this->addAttributeToFilter('created_at', array('from' => $days));
    }

        public function addStoreFilter($store) {
        return $this->addAttributeToFilter('main_table.store_id', $store->getId());
    }

    public function addGtsExportedFilter($gtsExport) {
        return $this->addAttributeToFilter('main_table.gts_exported', $gtsExport);
    }

    public function addTypeFilter($type) {
        return $this->addAttributeToFilter('main_table.state', $type);
    }

}

