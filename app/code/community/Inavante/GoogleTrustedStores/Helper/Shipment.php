<?php

class Inavante_GoogleTrustedStores_Helper_Shipment extends Mage_Core_Helper_Abstract {

//    public function includeRMA($type) {
//        return ($type == 'canceled' && $this->isRmaEE());
//    }

    public function GetTrackContent($shipment, $orderIncrementId) {
        if ($shipment) {
            $contents = array();
            $shipmentTrack = $this->GetTrack($shipment);
            // format: [ID],[TrackNumber],[Carrier_Code],[Other_Carrier_Code],[date]
            //[ID]
            $contents[0] = $orderIncrementId;
            if ($shipmentTrack) {

                //[TrackNumber]
                $trackNumber = ($shipmentTrack->getTrackNumber()) ? $shipmentTrack->getTrackNumber() : '';
                $contents[1] = $trackNumber;
                //[Carrier_Code]
                $trackCode = $this->getTrackCode($shipmentTrack->getCarrierCode());
                $contents[2] = $trackCode;

                //[Other_Carrier_Code]
                if ($trackCode == 'Other') {
                    $trackCodeOther = $this->getTrackOtherCode($shipmentTrack->getCarrierCode(), $shipmentTrack->getTitle());
                    $contents[3] = $trackCodeOther;
                }else
                    $contents[3] = '';
            }
            //[date]
            $contents[4] = Mage::app()->getLocale()->date(Varien_Date::formatDate($shipment->getCreatedAt()))->toString('yyyy-MM-dd');

            return $contents;
        }
    }

    private function getTrackCode($code) {

        $code = strtolower($code);

        switch ($code) {
            case 'ups':
                $code = 'UPS';
                break;
            case 'fedex':
                $code = 'FedEx';
                break;
            case 'usps':
                $code = 'USPS';
                break;

            default:
                $code = 'Other';
                break;
        }

        return $code;
    }

    private function getTrackOtherCode($code, $title) {

        $code = strtoupper($code);

        if (!$code || $code == "OTHER") {
            if ($title != "") {
                return $title;
            } else {
                return "OTHER";
            }
        }
        return $code;
    }

    private function GetTrack($shipment) {

        $shipmentTrack = Mage::getResourceModel('sales/order_shipment_track_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('parent_id', $shipment->getId())
                ->setPage(1, 1)
                ->getFirstItem();

        return $shipmentTrack;
    }

}