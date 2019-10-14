<?php

namespace LtcKomfortkasse\Subscriber;

class Order implements \Enlight\Event\SubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array ('Shopware_Modules_Order_SaveOrder_ProcessDetails' => array ('onInsertOrder',99999
        )
        );

    }


    public function onInsertOrder(\Enlight_Event_EventArgs $args)
    {
        $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('LtcKomfortkasse');
        if (!$config ['active']) {
            return;
        }
        $order = $args->getSubject();
        $orderNumber = $order->sOrderNumber;
        $orderId = $args->get('orderId');
        $shopurl = Shopware()->Db()->fetchOne("SELECT s.host FROM s_core_shops s join s_order o on s.id=o.subshopID WHERE o.id = '" . $orderId . "'");

        Shopware()->PluginLogger()->info('komfortkasse subscriber insert ' . $orderNumber);

        // notify Komfortkasse about order

        if ($orderNumber) {
            Shopware()->PluginLogger()->info('komfortkasse notify number ' . $orderNumber);
            $query = http_build_query(array ('id' => $orderId, 'number' => $orderNumber, 'url' => $shopurl
            ));
            $contextData = array ('method' => 'POST','timeout' => 2,'header' => "Connection: close\r\n" . 'Content-Length: ' . strlen($query) . "\r\n",'content' => $query
            );
            $context = stream_context_create(array ('http' => $contextData
            ));
            $result = @file_get_contents('http://api.komfortkasse.eu/api/shop/neworder.jsf', false, $context);
            return;
        }

    }
}