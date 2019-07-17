<?php

/**
 * Class Shopware_Controllers_Api_OrderId
 */
class Shopware_Controllers_Api_OrderId extends Shopware_Controllers_Api_Rest
{
    /**
     * GET Request on /api/orderId
     */
    public function indexAction()
    {
    }

    /**
     * Check if order exists
     *
     * GET /api/orderId/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        $useNumberAsId = $this->Request()->getParam('useNumberAsId');
        $subshops = $this->Request()->getParam('subshops');

        $sql = 'select id from s_order where ';
        $sql .= ($useNumberAsId == 'true' ? 'ordernumber' : 'id');
        $sql .= '=\'' . $id . '\'';
        if ($subshops)
            $sql .= ' and subshopID in (' . $subshops . ')';

        $id = Shopware()->Db()->fetchOne($sql);
        $data['id'] = ($id === false ? null : $id);

        $this->View()->assign(['success' => true, 'data' => $data]);
    }


}