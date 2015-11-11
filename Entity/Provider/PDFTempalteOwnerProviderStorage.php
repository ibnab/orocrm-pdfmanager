<?php

namespace Ibnab\Bundle\PmanagerBundle\Entity\Provider;

/**
 * A storage of pdftemplate owner providers
 */
class PDFTempalteOwnerProviderStorage
{
    /**
     * @var PDFTempalteOwnerProviderInterface[]
     */
    private $pdftemplateOwnerProviders = array();

    /**
     * Add pdftemplate owner provider
     *
     * @param PDFTempalteOwnerProviderInterface $provider
     */
    public function addProvider(PDFTempalteOwnerProviderInterface $provider)
    {
        $this->pdftemplateOwnerProviders[] = $provider;
    }

    /**
     * Get all pdftemplate owner providers
     *
     * @return PDFTempalteOwnerProviderInterface[]
     */
    public function getProviders()
    {
        return $this->pdftemplateOwnerProviders;
    }

    /**
     * Gets field name for pdftempalte owner for the given provider
     *
     * @param PDFTempalteOwnerProviderInterface $provider
     * @return string
     * @throws \RuntimeException
     */
    public function getPDFTemplateOwnerFieldName(PDFTempalteOwnerProviderInterface $provider)
    {
        $key = 0;
        for ($i = 0, $size = count($this->pdftemplateOwnerProviders); $i < $size; $i++) {
            if ($this->pdftemplateOwnerProviders[$i] === $provider) {
                $key = $i + 1;
                break;
            }
        }

        if ($key === 0) {
            throw new \RuntimeException(
                'The provider for "%s" must be registers in PDFTempalteOwnerProviderStorage',
                $provider->getPDFTemplateOwnerClass()
            );
        }

        return sprintf('owner%d', $key);
    }

    /**
     * Gets column name for pdftemplate owner for the given provider
     *
     * @param PDFTempalteOwnerProviderInterface $provider
     * @return string
     */
    public function getPDFTemplateOwnerColumnName(PDFTempalteOwnerProviderInterface $provider)
    {
        $PDFTemplateOwnerClass = $provider->getPDFTemplateOwnerClass();
        $prefix = strtolower(substr($PDFTemplateOwnerClass, 0, strpos($PDFTemplateOwnerClass, '\\')));
        if ($prefix === 'oro' || $prefix === 'orocrm') {
            // do not use prefix if pdftemplate's owner is a part of BAP and CRM
            $prefix = '';
        } else {
            $prefix .= '_';
        }
        $suffix = strtolower(substr($PDFTemplateOwnerClass, strrpos($PDFTemplateOwnerClass, '\\') + 1));

        return sprintf('owner_%s%s_id', $prefix, $suffix);
    }
}
