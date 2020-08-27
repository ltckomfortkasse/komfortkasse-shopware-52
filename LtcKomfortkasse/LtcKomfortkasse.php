<?php

namespace LtcKomfortkasse;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class LtcKomfortkasse extends \Shopware\Components\Plugin
{


    public static function getSubscribedEvents()
    {
        return [ 'Enlight_Controller_Action_PostDispatch_Frontend_Checkout' => 'onPostDispatchCheckout','Shopware\Models\Order\Order::postUpdate' => 'updateOrder','Enlight_Controller_Dispatcher_ControllerPath_Api_Document' => 'onDocumentApiController',
                'Enlight_Controller_Dispatcher_ControllerPath_Api_LtcKomfortkasseVersion' => 'onDocumentApiLtcKomfortkasseVersion','Enlight_Controller_Dispatcher_ControllerPath_Api_Refund' => 'onRefundApiController',
                'Enlight_Controller_Dispatcher_ControllerPath_Api_OrderStatus' => 'onOrderStatusApiController','Enlight_Controller_Dispatcher_ControllerPath_Api_PaymentStatus' => 'onPaymentStatusApiController',
                'Enlight_Controller_Dispatcher_ControllerPath_Api_PaymentMeans' => 'onPaymentMeansApiController','Enlight_Controller_Dispatcher_ControllerPath_Api_OrderId' => 'onOrderIdApiController','Enlight_Controller_Front_StartDispatch' => 'onEnlightControllerFrontStartDispatch'
        ];

    }


    public function onRefundApiController()
    {
        return $this->getPath() . '/Controllers/Api/Refund.php';

    }


    public function onOrderStatusApiController()
    {
        return $this->getPath() . '/Controllers/Api/OrderStatus.php';

    }


    public function onPaymentStatusApiController()
    {
        return $this->getPath() . '/Controllers/Api/PaymentStatus.php';

    }


    public function onPaymentMeansApiController()
    {
        return $this->getPath() . '/Controllers/Api/PaymentMeans.php';

    }


    public function onOrderIdApiController()
    {
        return $this->getPath() . '/Controllers/Api/OrderId.php';

    }


    public function onDocumentApiController()
    {
        return $this->getPath() . '/Controllers/Api/Document.php';

    }


    public function onDocumentApiLtcKomfortkasseVersion()
    {
        return $this->getPath() . '/Controllers/Api/LtcKomfortkasseVersion.php';

    }


    public function onEnlightControllerFrontStartDispatch()
    {
        $this->container->get('loader')->registerNamespace('Shopware\Components', $this->getPath() . '/Components/');

    }


    public function updateOrder($arguments)
    {
        $order = $arguments->get('entity');

        $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('LtcKomfortkasse', $order->getShop());
        if (!$config ['active']) {
            return;
        }

        Shopware()->PluginLogger()->info('komfortkasse updateorder ' . $order->getNumber());

        // if order is new: notify Komfortkasse about order

        if ($order->getNumber()) {
            $historyList = $order->getHistory();
            $count = $historyList === null ? null : $historyList->count();
            Shopware()->PluginLogger()->info('komfortkasse count ' . $count);
            if ($count === 1)
                Shopware()->PluginLogger()->info('komfortkasse last status ' . $historyList->last()->getPreviousPaymentStatus()->getId());
            if ($count === null || $count === 0 || ($count === 1 && $historyList->last()->getPreviousPaymentStatus()->getId() == 0)) {
                Shopware()->PluginLogger()->info('komfortkasse notify id ' . $order->getId());
                $shopurl = Shopware()->Db()->fetchOne("SELECT s.host FROM s_core_shops s join s_order o on s.id=o.subshopID WHERE o.id = " . $order->getId());
                $query = http_build_query(array ('id' => $order->getId(),'number' => $order->getNumber(),'url' => $shopurl
                ));
                $contextData = array ('method' => 'POST','timeout' => 2,'header' => "Connection: close\r\n" . 'Content-Length: ' . strlen($query) . "\r\n",'content' => $query
                );
                $context = stream_context_create(array ('http' => $contextData
                ));
                $result = @file_get_contents('http://api.komfortkasse.eu/api/shop/neworder.jsf', false, $context);
                return;
            }
        }

        // if order has been cancelled: cancel details in pickware/shopware erp

        if (!$config ['cancelDetail']) {
            return;
        }

        // only if cancelled with Komfortkasse transaction

        if (strpos($order->getTransactionID(), 'Komfortkasse') === false)
            return;

        $history = $order->getHistory()->last();
        if ($history && $history->getPreviousOrderStatus()->getId() != 4 && $history->getOrderStatus()->getId() == 4) {

            if (method_exists('Shopware\Models\Attribute\OrderDetail', 'setViisonCanceledQuantity')) {

                // old Pickware Versions

                $em = Shopware()->Container()->get('models');
                foreach ($order->getDetails()->toArray() as $detail) {
                    $attr = $detail->getAttribute();
                    if ($attr) {
                        $qty = $detail->getQuantity();
                        $detail->setQuantity(0);
                        $detail->setShipped(0);
                        $attr->setViisonCanceledQuantity($attr->getViisonCanceledQuantity() + $qty);
                        // ab Shopware 5.2 existiert die Methode setViisonPickedQuantity nicht mehr
                        if (method_exists('Shopware\Models\Attribute\OrderDetail', 'setViisonPickedQuantity'))
                            $attr->setViisonPickedQuantity(0);
                        $em->persist($detail);
                    }
                }
                $em->flush();

            } else {

                // Pickware >= 6.0.0

                try {

                    $orderCancelerService = $this->container->get('pickware.erp.order_canceler_service');
                    Shopware()->PluginLogger()->info('komfortkasse cancel pickware details ' . $order->getNumber());
                    foreach ($order->getDetails()->toArray() as $orderDetail) {
                        $orderCancelerService->cancelRemainingQuantityToShipOfOrderDetail($orderDetail, $orderDetail->getQuantity());
                    }
                } catch ( ServiceNotFoundException $e ) {
                    Shopware()->PluginLogger()->warn('komfortkasse cancel pickware details error: service not found (maybe using pickware below 6.0.0.?)');
                }
            }
        }

    }


    public function onPostDispatchCheckout($arguments)
    {
        $subject = $arguments->getSubject();
        $request = $subject->Request();
        $action = $request->getActionName();

        if ($action === 'finish') {

            Shopware()->PluginLogger()->info('komfortkasse onPostDispatchCheckout');

            $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('LtcKomfortkasse');
            if (!$config ['active']) {
                return;
            }

            $ordernum = $_SESSION ['Shopware'] ['sOrderVariables']->sOrderNumber;
            if ($ordernum) {
                $shopurl = Shopware()->Db()->fetchOne("SELECT s.host FROM s_core_shops s join s_order o on s.id=o.subshopID WHERE o.ordernumber = " . $ordernum);
                $query = http_build_query(array ('number' => $ordernum,'url' => $shopurl
                ));
            } else {
                $temp_id = $_SESSION ['Shopware'] ['sessionId'];
                if ($temp_id) {
                    $id = Shopware()->Db()->fetchOne("SELECT id FROM s_order WHERE temporaryID = ?", array ($temp_id
                    ));
                    if ($id) {
                        $shopurl = Shopware()->Db()->fetchOne("SELECT s.host FROM s_core_shops s join s_order o on s.id=o.subshopID WHERE o.id = " . $id);
                        $query = http_build_query(array ('id' => $id,'url' => $shopurl
                        ));
                    }
                }
            }
            if ($query) {
                $contextData = array ('method' => 'POST','timeout' => 2,'header' => "Connection: close\r\n" . 'Content-Length: ' . strlen($query) . "\r\n",'content' => $query
                );
                $context = stream_context_create(array ('http' => $contextData
                ));

                $result = @file_get_contents('http://api.komfortkasse.eu/api/shop/neworder.jsf', false, $context);
            }
        }

    }
}
