<?php

/**
 * Description of 
 * @package   Inavante_GoogleTrustedStores
 * @company   Inavante - http://www.cueblocks.com/
 * @author    Francesco Magazzu' <francesco.magazzu at cueblocks.com>
 */
class Inavante_GoogleTrustedStores_Block_Adminhtml_GoogleTrustedStores_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{

    public function render(Varien_Object $row)
    {
        /* @var $row Inavante_GoogleTrustedStores_Model_GoogleTrustedStores */
        
        $this->getColumn()->setActions(
                array(
                    array(
                        'url' => $this->getUrl("*/Googletrustedstores/ping", array("sitemap_id" => $row->getSitemapId())),
                        'caption'    => Mage::helper('adminhtml')->__('Ping Sitemap'),
                    ), array(
                        'url' => $this->getUrl('*/Googletrustedstores/generate', array('sitemap_id' => $row->getSitemapId())),
                        'caption'    => Mage::helper('GoogleTrustedStores')->__('Generate'),
                        'confirm'    => Mage::helper('adminhtml')->__('Are you sure you want to update/generate this XML Sitemap?'),
                    )
//                    , array(
//                        'url' => $this->getUrl('*/Googletrustedstores/generatepopup', array('sitemap_id' => $row->getSitemapId())),
//                        'caption'    => Mage::helper('GoogleTrustedStores')->__('Generate Pop Up'),
//                        'confirm'    => Mage::helper('adminhtml')->__('Are you sure you want to update/generate this XML Sitemap?'),
//                        'popup'      => true
//                    )
                    , array(
                        'url' => $this->getUrl('*/Googletrustedstores/delete', array('sitemap_id' => $row->getSitemapId())),
                        'caption'    => Mage::helper('GoogleTrustedStores')->__('Delete'),
                        'confirm'    => Mage::helper('adminhtml')->__('Are you sure you want to delete this XML Sitemap?'),
                    )
//                    , array(
//                        'url' => $this->getUrl('*/Googletrustedstores/addRobots', array('sitemap_id' => $row->getSitemapId())),
//                        'caption'    => Mage::helper('GoogleTrustedStores')->__('Add to Robots.txt'),
//                    )
                )
        );
        return parent::render($row);
    }

}
