<?php

class Inavante_GoogleTrustedStores_Block_Js extends Mage_Core_Block_Text {
    /* (non-PHPdoc)
     * @see Mage_Core_Block_Text::_toHtml()
     */

    protected function _toHtml() {
        if (!Mage::getStoreConfigFlag('googletrustedstores/config/enabled', Mage::app()->getStore()->getId())) {
            return '';
        }

        $accountId = Mage::getStoreConfig('googletrustedstores/config/account_id', Mage::app()->getStore()->getId());

        $html = <<<JS
<!-- BEGIN: Google Trusted Store -->
<script type="text/javascript">
  var gts = gts || [];

  gts.push(["id", "{$accountId}"]);

  (function() {
    var scheme = (("https:" == document.location.protocol) ? "https://" : "http://");
    var gts = document.createElement("script");
    gts.type = "text/javascript";
    gts.async = true;
    gts.src = scheme + "www.googlecommerce.com/trustedstores/gtmp_compiled.js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(gts, s);
  })();
</script>
<!-- END: Google Trusted Store -->
JS;
        if (!$this->getOrderIds()) {
            return $html;
        }

        foreach ($this->getOrderIds() as $orderId) {
            $order = Mage::getModel('sales/order')
                    ->load($orderId);
            if (!$order->getId()) {
                continue;
            }

            $htmlOrderItems = '';
            foreach ($order->getAllItems() as $item) {
                $htmlOrderItems .= <<<SPAN
<span class="gts-item">
    <span class="gts-i-name">{$item->getName()}</span>
    <span class="gts-i-price">{$item->getPrice()}</span>
    <span class="gts-i-quantity">{$item->getQtyOrdered()}</span>
</span>
SPAN;
            }

            $urlArray        = parse_url(Mage::getStoreConfig('web/unsecure/base_url', $order->getStoreId()));
            $domain          = $urlArray['host'];
            $estShipDateDays = Mage::getStoreConfig('googletrustedstores/config/delivery_weekdays', $order->getStoreId());
            $estShipDateDate = Mage::app()->getLocale()->date()->addWeekday($estShipDateDays)->toString('yyyy-MM-dd');

            $html .= <<<DIV
<!-- START Trusted Stores Order -->
<div id="gts-order" style="display:none;">
  <!-- start order and merchant information -->
  <span id="gts-o-id">{$order->getIncrementId()}</span>
  <span id="gts-o-domain">{$domain}</span>
  <span id="gts-o-email">{$order->getCustomerEmail()}</span>
  <span id="gts-o-country">{$order->getBillingAddress()->getCountryId()}</span>
  <span id="gts-o-currency">{$order->getOrderCurrencyCode()}</span>
  <span id="gts-o-total">{$order->getGrandTotal()}</span>
  <span id="gts-o-discounts">{$order->getDiscountAmount()}</span>
  <span id="gts-o-shipping-total">{$order->getShippingAmount()}</span>
  <span id="gts-o-tax-total">{$order->getTaxAmount()}</span>
  <span id="gts-o-est-ship-date">{$estShipDateDate}</span>
  <span id="gts-o-has-preorder">N</span>
  <span id="gts-o-has-digital">N</span>
  <!-- end order and merchant information -->

  <!-- start repeated item specific information -->
  {$htmlOrderItems}
  <!-- end repeated item specific information -->

</div>
<!-- END Trusted Stores -->
DIV;
            break;
        }
        return $html;
    }

}
