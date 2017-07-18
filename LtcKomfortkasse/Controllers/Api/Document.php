<?php

/**
 * Class Shopware_Controllers_Api_Document
 */
class Shopware_Controllers_Api_Document extends Shopware_Controllers_Api_Rest
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
     * GET Request on /api/Document
     */
    public function indexAction()
    {
        $limit = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort = $this->Request()->getParam('sort', []);
        $filter = $this->Request()->getParam('filter', []);

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign(['success' => true, 'data' => $result]);
    }

    /**
     * Get one Document
     *
     * GET /api/Document/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        /** @var \Shopware\Models\Order\Document $document */
        $document = $this->resource->getOne($id);

        $this->View()->assign(['success' => true, 'data' => $document]);
    }


}