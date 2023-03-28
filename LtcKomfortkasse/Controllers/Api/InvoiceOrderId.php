<?php

/**
 * Class Shopware_Controllers_Api_InvoiceOrderId
 */
class Shopware_Controllers_Api_InvoiceOrderId extends Shopware_Controllers_Api_Rest
{

    /**
     * GET Request on /api/invoiceOrderId
     */
    public function indexAction()
    {}

    /**
     * Check if order exists
     *
     * GET /api/invoiceOrderId/{id}
     */
    public function getAction()
    {
        $invoiceNumber = $this->Request()->getParam('id');
        $doctypes = $this->Request()->getParam('doctypes');
        $doctypes_array = $doctypes ? explode(',', $doctypes) : array();
        $subshops = $this->Request()->getParam('subshops');
        $subshops_array = $subshops ? array_map('intval', explode(',', $subshops)) : array();

        $sql = 'select o.id FROM s_order_documents od join s_core_documents d on d.id=od.type join s_order o on o.id=od.orderID where od.docID=?';
        if ($doctypes)
            $sql .= ' and d.key in (' . str_repeat('?,', count($doctypes_array) - 1) . '?)';
        if ($subshops)
            $sql .= ' and subshopID in (' . str_repeat('?,', count($subshops_array) - 1) . '?)';

        $stmt = Shopware()->Db()->prepare($sql);

        $stmt->bindValue(1, $invoiceNumber);
        for ($i = 0; $i < count($doctypes_array); $i ++)
            $stmt->bindValue($i + 2, $doctypes_array[$i]);
        for ($j = 0; $j < count($subshops_array); $j ++)
            $stmt->bindValue($j + $i + 2, $subshops_array[$j]);

        $stmt->execute();
        $result = $stmt->fetch();
        $id = $result['id'];
        $data['id'] = ($id === false ? null : $id);

        $this->View()->assign([
            'success' => true,
            'data' => $data
        ]);
    }
}