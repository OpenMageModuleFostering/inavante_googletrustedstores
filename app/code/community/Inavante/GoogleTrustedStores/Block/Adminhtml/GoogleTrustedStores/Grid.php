<?php

/**
 * Description of 
 * @package   Inavante_GoogleTrustedStores
 * @company   Inavante - http://www.cueblocks.com/
 * @author    Francesco Magazzu' <francesco.magazzu at cueblocks.com>
 */
class Inavante_GoogleTrustedStores_Block_Adminhtml_GoogleTrustedStores_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('googletrustedstores');
        $this->setDefaultSort('id');

//        $urlNew    = $this->getUrl('*/Googletrustedstores/new');
//        $urlConfig = $this->getUrl('*/system_config/edit/section/Googletrustedstores');

        $this->_emptyText = Mage::helper('adminhtml')->__('No log for this feed');
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('googletrustedstores/log')->getCollection()
                ->addFieldToFilter('status', $this->getLogType());

        /* @var $collection Mage_Sitemap_Model_Mysql4_Sitemap_Collection */

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('googletrustedstores')->__('Log ID'),
            'index' => 'id',
            'width' => '16px',
        ));
        $this->addColumn('order_id', array(
            'header' => Mage::helper('googletrustedstores')->__('Order ID'),
            'index' => 'order_id',
            'width' => '16px',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('googletrustedstores')->__('Status'),
            'index' => 'status',
        ));

        if ($this->getLogType() == 'shipped') {

            $this->addColumn('shipment_title', array(
                'header' => Mage::helper('googletrustedstores')->__('Carrier'),
                'index' => 'shipment_title',
            ));
            $this->addColumn('track_number', array(
                'header' => Mage::helper('googletrustedstores')->__('Track Number'),
                'index' => 'track_number',
            ));
        } else if ($this->getLogType() == 'canceled') {

            $this->addColumn('cancel_reason', array(
                'header' => Mage::helper('googletrustedstores')->__('Cancel Reason'),
                'index' => 'cancel_reason',
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('googletrustedstores')->__('Exported On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px'
        ));

        return parent::_prepareColumns();
    }

}
