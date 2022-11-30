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
        $subshops_array = $subshops ? array_map('intval', explode(',', $subshops)) : null;

        $sql = 'select id from s_order where ';
        $sql .= ($useNumberAsId == 'true' ? 'ordernumber' : 'id');
        $sql .= '=?';
        if ($subshops)
            $sql .= ' and subshopID in (' . str_repeat('?,', count($subshops_array) - 1) . '?)';

        $stmt = Shopware()->Db()->prepare($sql);

        $stmt->bindValue(1, $id);
        for($i = 0; $i < count($subshops_array); $i++)
            $stmt->bindValue($i + 2, $subshops_array [$i]);

        $stmt->execute();
        $result = $stmt->fetch();
        $id = $result ['id'];
        $data ['id'] = ($id === false ? null : $id);

        $this->View()->assign([ 'success' => true,'data' => $data
        ]);
    }
}