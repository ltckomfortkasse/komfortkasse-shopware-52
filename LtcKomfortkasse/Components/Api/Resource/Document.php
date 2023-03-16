<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use Shopware\Models\Order\Document\Document as DocumentModel;

/**
 * Class Document
 *
 * @package Shopware\Components\Api\Resource
 */
class Document extends Resource
{

    /**
     * @return \Shopware\Models\Order\Document\Repository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(DocumentModel::class);
    }

    public function getList($offset = 0, $limit = 25, array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('document');

        $builder->addFilter($criteria)
        ->addOrderBy($orderBy)
        ->setFirstResult($offset)
        ->setMaxResults($limit);
        $query = $builder->getQuery();
        $query->setHydrationMode($this->resultMode);

        $paginator = $this->getManager()->createPaginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns the Document data
        $document = $paginator->getIterator()->getArrayCopy();

        return ['data' => $document, 'total' => $totalResult];
    }

    public function getOne($id)
    {
        $this->checkPrivilege('read');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $builder = $this->getRepository()
        ->createQueryBuilder('Document')
        ->select('Document')
        ->where('Document.id = ?1')
        ->setParameter(1, $id);

        /** @var DocumentModel $document */
        $document = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$document) {
            throw new ApiException\NotFoundException("Document by id $id not found");
        }

        $file = Shopware()->DocPath('files/documents') . $document['hash'] . '.pdf';
        if (file_exists($file)) {
            $document['pdf_base64'] = base64_encode(file_get_contents($file));
        }

        return $document;
    }

}

