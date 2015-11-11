<?php

namespace Ibnab\Bundle\PmanagerBundle\Entity\Provider;

use Doctrine\ORM\EntityManager;
use Ibnab\Bundle\PmanagerBundle\Entity\PDFTemplateOwnerInterface;
use Ibnab\Bundle\PmanagerBundle\Entity\Provider\PDFTempalteOwnerProviderStorage;
/**
 * PDFTemplate owner provider chain
 */
class PDFTempalteOwnerProvider
{
    /**
     * @var PDFTempalteOwnerProviderStorage
     */
    private $pdftemplateOwnerProviderStorage;

    /**
     * Constructor
     *
     * @param PDFTempalteOwnerProviderStorage $PDFTemplateOwnerProviderStorage
     */
    public function __construct(PDFTempalteOwnerProviderStorage $PDFTemplateOwnerProviderStorage)
    {
        $this->pdftemplateOwnerProviderStorage = $PDFTemplateOwnerProviderStorage;
    }

    /**
     * Find an entity object which is an owner of the given email address
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $ptemplate
     * @return PDFTemplateOwnerInterface
     */
    public function findPDFTemplatelOwner(EntityManager $em, $ptemplate)
    {
        $ptemplateOwner = null;
        foreach ($this->pdftemplateOwnerProviderStorage->getProviders() as $provider) {
            $ptemplateOwner = $provider->findPDFTemplateOwner($em, $ptemplate);
            if ($ptemplateOwner !== null) {
                break;
            }
        }

        return $ptemplateOwner;
    }
}
