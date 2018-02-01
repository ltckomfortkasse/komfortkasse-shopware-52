<?php

namespace LtcKomfortkasse;

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
        $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('LtcKomfortkasse');
        if (!$config ['active']) {
            return;
        }

        $order = $arguments->get('entity');

        Shopware()->PluginLogger()->info('komfortkasse updateorder ' . $order->getNumber());

        // if order is new: notify Komfortkasse about order

        if ($order->getNumber()) {
            $historyList = $order->getHistory();
            Shopware()->PluginLogger()->info('komfortkasse count ' . $historyList->count());
            if ($historyList->count() == 1)
                Shopware()->PluginLogger()->info('komfortkasse last status ' . $historyList->last()->getPreviousPaymentStatus()->getId());
            if ($historyList->count() == 0 || ($historyList->count() == 1 && $historyList->last()->getPreviousPaymentStatus()->getId() == 0)) {
                Shopware()->PluginLogger()->info('komfortkasse notify id ' . $order->getId());
                $site_url = Shopware()->System()->sCONFIG ["sBASEPATH"];
                $query = http_build_query(array ('id' => $order->getId(),'url' => $site_url
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
        if (!method_exists('Shopware\Models\Attribute\OrderDetail', 'setViisonCanceledQuantity'))
            return;

        if (strpos($order->getTransactionID(), 'Komfortkasse') === false)
            return;

        $history = $order->getHistory()->last();
        if ($history && $history->getPreviousOrderStatus()->getId() != 4 && $history->getOrderStatus()->getId() == 4) {
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

            $site_url = Shopware()->System()->sCONFIG ["sBASEPATH"];
            $ordernum = $_SESSION ['Shopware'] ['sOrderVariables']->sOrderNumber;
            if ($ordernum) {
                $query = http_build_query(array ('number' => $ordernum,'url' => $site_url
                ));
            } else {
                $temp_id = $_SESSION ['Shopware'] ['sessionId'];
                $id = Shopware()->Db()->fetchOne("SELECT id FROM s_order WHERE temporaryID = ?", array ($temp_id
                ));
                $query = http_build_query(array ('id' => $id,'url' => $site_url
                ));
            }
            $contextData = array ('method' => 'POST','timeout' => 2,'header' => "Connection: close\r\n" . 'Content-Length: ' . strlen($query) . "\r\n",'content' => $query
            );
            $context = stream_context_create(array ('http' => $contextData
            ));

            $result = @file_get_contents('http://api.komfortkasse.eu/api/shop/neworder.jsf', false, $context);
        }

    }
}
