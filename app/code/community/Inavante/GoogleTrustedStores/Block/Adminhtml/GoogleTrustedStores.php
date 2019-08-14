<?php

/**
 * Description of 
 * @package   Inavante_GoogleTrustedStores
 * @company   Inavante - http://www.cueblocks.com/
 * @author    Francesco Magazzu' <francesco.magazzu at cueblocks.com>
 * 
 */
class Inavante_GoogleTrustedStores_Block_Adminhtml_GoogleTrustedStores extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Block constructor
     */
    public function __construct() {

        parent::__construct();

        $this->_controller = 'adminhtml_googleTrustedStores';
        $this->_blockGroup = 'googletrustedstores';

        $this->_removeButton('add');
    }

    //set the log/grid type on the base of XML Layout
    public function renderView() {
        
        $type = $this->getLogType();

        $this->_headerText = Mage::helper('googletrustedstores')->__('Feed Log - ' . $type . ' orders');
        $this->getLayout()->getBlock($this->_controller . '.grid')->setLogType($type);
        
        return parent::renderView();
    }

}
