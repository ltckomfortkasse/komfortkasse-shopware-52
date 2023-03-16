<?php

/**
 * Class Shopware_Controllers_Api_Refund
 */
class Shopware_Controllers_Api_Refund extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\Document
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('Document');
    }

    /**
     * GET Request on /api/Refund
     */
    public function indexAction()
    {
        $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('LtcKomfortkasse');
        $docType = $config ['refundDocumentType'];
        if (!$docType) {
            $result['success'] = false;
            $this->View()->assign($result);
            return;
        }

        $limit = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort = $this->Request()->getParam('sort', []);
        $filter = $this->Request()->getParam('filter', []);

        $c = count($filter);
        $filter[$c]['property'] = 'type';
        $filter[$c]['expression'] = '=';
        $filter[$c]['value'] = $docType;
        $c++;
        $filter[$c]['property'] = 'date';
        $filter[$c]['expression'] = '>';
        $filter[$c]['value'] = date('Y-m-d', strtotime('-7 days'));

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $result['success'] = true;
        $this->View()->assign($result);
    }

    /**
     * Get one Document
     *
     * GET /api/Refund/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        /** @var \Shopware\Models\Order\Document $document */
        $document = $this->resource->getOne($id);

        $this->View()->assign(['success' => true, 'data' => $document]);
    }


}