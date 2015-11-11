<?php

namespace Ibnab\Bundle\PmanagerBundle\Entity\Provider;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\EmailBundle\Entity\PDFTemplateOwnerInterface;

/**
 * Defines an interface of an pdftemplate owner provider
 */
interface PDFTempalteOwnerProviderInterface
{
    /**
     * Get full name of pdftemplate owner class
     *
     * @return string
     */
    public function getPDFTemplateOwnerClass();

    /**
     * Find an entity object which is an owner of the given email address
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $pdftemplate
     * @return PDFTemplateOwnerInterface
     */
    public function findPDFTemplateOwner(EntityManager $em, $pdftemplate);
}
