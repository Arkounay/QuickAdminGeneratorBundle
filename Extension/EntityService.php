<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Doctrine\ORM\EntityManagerInterface;

readonly class EntityService
{

    public function __construct(private EntityManagerInterface $em) {}

    public function getId(object $entity): mixed
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));
        $idField = $metadata->getSingleIdentifierFieldName();
        return $metadata->getFieldValue($entity, $idField);
    }

}
