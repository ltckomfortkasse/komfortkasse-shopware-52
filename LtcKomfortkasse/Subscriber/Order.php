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

        $stmt = Shopware()->Db()->prepare('SELECT s.host FROM s_core_shops s join s_order o on s.id=o.subshopID WHERE o.id = ?');
        $stmt->bindValue(1, $orderId);
        $stmt->execute();
        $result = $stmt->fetch();
        $shopurl = $result ['host'];

        Shopware()->Container()->get('pluginlogger')->info('komfortkasse subscriber insert ' . $orderNumber);

        // notify Komfortkasse about order

        if ($orderNumber) {
            Shopware()->Container()->get('pluginlogger')->info('komfortkasse notify number ' . $orderNumber);
            \LtcKomfortkasse\LtcKomfortkasse::notifyKomfortkasse(array ('id' => $orderId, 'number' => $orderNumber, 'url' => $shopurl
            ));
            return;
        }

    }
}