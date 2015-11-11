<?php

namespace Ibnab\Bundle\PmanagerBundle\Placeholder;

use Doctrine\Common\Util\ClassUtils;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Ibnab\Bundle\PmanagerBundle\Provider\ConfigurationProvider;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Ibnab\Bundle\PmanagerBundle\Entity\PDFTemplate;

class PlaceholderFilter
{

    /** @var ConfigProvider */
    protected $configProvider;

    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /**
     * @param ConfigProvider $entityConfigProvider
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(
        ConfigurationProvider $configProvider,
        DoctrineHelper $doctrineHelper
    ) {
        $this->configProvider = $configProvider;
        $this->doctrineHelper       = $doctrineHelper;
    }
    public function getAllowedSection()
    {
      $values['allowed'] = ['OroCRM\Bundle\ContactBundle\Entity\Contact',
                             'OroCRM\Bundle\MagentoBundle\Entity\Order',
                             'OroCRM\Bundle\SalesBundle\Entity\Lead',
                             'OroCRM\Bundle\SalesBundle\Entity\Opportunity',
                             'OroCRM\Bundle\AccountBundle\Entity\Account',
                             'OroCRM\Bundle\MagentoBundle\Entity\Customer',
                             'OroCRM\Bundle\MagentoBundle\Entity\Cart'];
      return $values;
    }
    /**
     *
     * @param object $entity
     * @return bool
     */
    public function isApplicable($entity)
    {
        if (!is_object($entity)
            || !$this->doctrineHelper->isManageableEntity($entity)
            || $this->doctrineHelper->isNewEntity($entity)
        ) {
            return false;
        }
        //$allowedValues = $this->configProvider->getAllowed();
        $allowedSection = $this->getAllowedSection();
        $className = ClassUtils::getClass($entity);
        $allowedSection = $allowedSection['allowed'];
        foreach($allowedSection as $allowedValue)
        {
          if($allowedValue == $className)
           {
             
             return true;
           }
        }
        //echo $className;die();
        return false;
    }
}
