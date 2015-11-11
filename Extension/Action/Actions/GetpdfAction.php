<?php

namespace Ibnab\Bundle\PmanagerBundle\Extension\Action\Actions;

use Oro\Bundle\DataGridBundle\Extension\Action\ActionConfiguration;
use Oro\Bundle\DataGridBundle\Extension\Action\Actions\AjaxAction;
class GetpdfAction extends AjaxAction
{
    /**
     * @var array
     */
    protected $requiredOptions = [  'entityName', 'data_identifier'];


    public function getOptions()
    {
        $options = parent::getOptions();
        $options['frontend_type'] = 'getpdf';
        return $options;
    }
}
