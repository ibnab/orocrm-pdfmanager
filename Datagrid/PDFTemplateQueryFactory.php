<?php

namespace Ibnab\Bundle\PmanagerBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\LocaleBundle\DQL\DQLNameFormatter;
use Ibnab\Bundle\PmanagerBundle\Entity\Provider\PDFTempalteOwnerProviderStorage;

class PDFTemplateQueryFactory
{
    /** @var EmailOwnerProviderStorage */
    protected $ptemplateOwnerProviderStorage;

    /** @var DQLNameFormatter */
    protected $formatter;

    /** @var string */
    protected $fromEmailExpression;

    /**
     * @param EmailOwnerProviderStorage                     $emailOwnerProviderStorage
     * @param \Oro\Bundle\LocaleBundle\DQL\DQLNameFormatter $formatter
     */
    public function __construct(PDFTempalteOwnerProviderStorage $ptemplateOwnerProviderStorage, DQLNameFormatter $formatter)
    {
        $this->ptemplateOwnerProviderStorage = $ptemplateOwnerProviderStorage;
        $this->formatter                 = $formatter;
    }

    /**
     * @param QueryBuilder $qb                  Source query builder
     * @param string       $ptemplateFromTableAlias EmailAddress table alias of joined Email#fromEmailAddress association
     */
    public function prepareQuery(QueryBuilder $qb, $ptemplateFromTableAlias = 'a')
    {
        $qb->addSelect($this->getFromPDFTemplateExpression($ptemplateFromTableAlias));
        foreach ($this->ptemplateOwnerProviderStorage->getProviders() as $provider) {
            $fieldName = $this->ptemplateOwnerProviderStorage->getPDFTemplateOwnerFieldName($provider);

            $qb->leftJoin(sprintf('%s.%s', $ptemplateFromTableAlias, $fieldName), $fieldName);
        }
    }

    /**
     * @param string $ptemplateFromTableAlias EmailAddress table alias of joined Email#fromEmailAddress association
     *
     * @return string
     */
    protected function getFromPDFTemplateExpression($ptemplateFromTableAlias)
    {
        $providers = $this->ptemplateOwnerProviderStorage->getProviders();
        if (empty($providers)) {
            return sprintf('%s.pdftemplate', $ptemplateFromTableAlias);
        }

        $expressionsByOwner = [];
        foreach ($providers as $provider) {
            $relationAlias                      = $this->ptemplateOwnerProviderStorage->getPDFTemplateOwnerFieldName($provider);
            $expressionsByOwner[$relationAlias] = $this->formatter->getFormattedNameDQL(
                $relationAlias,
                $provider->getEmailOwnerClass()
            );
        }

        $expression = '';
        foreach ($expressionsByOwner as $alias => $expressionPart) {
            $expression .= sprintf('WHEN %s.%s IS NOT NULL THEN %s', $ptemplateFromTableAlias, $alias, $expressionPart);
        }
        $expression = sprintf('CASE %s ELSE \'\' END', $expression);

        // if has owner then use expression to expose formatted name, use email otherwise
        return sprintf(
            'CONCAT(\'\', CASE WHEN %1$s.hasOwner = true THEN (%2$s) ELSE %1$s.email END) as fromEmailExpression',
            $ptemplateFromTableAlias,
            $expression
        );
    }
}
