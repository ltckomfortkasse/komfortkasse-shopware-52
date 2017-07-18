<?php
namespace LtcKomfortkasse;

class LtcKomfortkasse extends \Shopware\Components\Plugin
{
    public static function getSubscribedEvents()
    {
        return [
                'Enlight_Controller_Action_PostDispatch_Frontend_Checkout' => 'onPostDispatchCheckout',
                'Shopware\Models\Order\Order::postUpdate' => 'updateOrder',
                'Enlight_Controller_Dispatcher_ControllerPath_Api_Document' => 'onDocumentApiController',
                'Enlight_Controller_Dispatcher_ControllerPath_Api_LtcKomfortkasseVersion' => 'onDocumentApiLtcKomfortkasseVersion',
                'Enlight_Controller_Front_StartDispatch' => 'onEnlightControllerFrontStartDispatch'
        ];
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

    public function updateOrder(\Enlight_Controller_EventArgs $arguments)
    {
        $config = $this->Config();
        if (empty($config->cancelDetail)) {
            return;
        }
        if (!method_exists('Shopware\Models\Attribute\OrderDetail', 'setViisonCanceledQuantity'))
            return;

        $order = $arguments->get('entity');
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


    public function onPostDispatchCheckout(\Enlight_Controller_EventArgs $arguments)
    {
        $config = $this->Config();
        if (empty($config->active)) {
            return;
        }
        $subject = $arguments->getSubject();
        $request = $subject->Request();
        $response = $subject->Response();
        $action = $request->getActionName();

        $temp_id = $_SESSION ['Shopware'] ['sessionId'];
        $id = Shopware()->Db()->fetchOne("SELECT id FROM s_order WHERE temporaryID = ?", array ($temp_id
        ));
        $site_url = Shopware()->System()->sCONFIG ["sBASEPATH"];

        if ($action === 'finish') {

            $query = http_build_query(array ('id' => $id,'url' => $site_url
            ));

            $contextData = array ('method' => 'POST','timeout' => 2,'header' => "Connection: close\r\n" . 'Content-Length: ' . strlen($query) . "\r\n",'content' => $query
            );

            $context = stream_context_create(array ('http' => $contextData
            ));

            $result = @file_get_contents('http://api.komfortkasse.eu/api/shop/neworder.jsf', false, $context);
        }

    }

}
