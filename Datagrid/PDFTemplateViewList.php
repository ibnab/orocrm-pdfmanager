<?php

namespace Ibnab\Bundle\PmanagerBundle\Datagrid;

use Oro\Bundle\DataGridBundle\Extension\GridViews\View;
use Oro\Bundle\DataGridBundle\Extension\GridViews\AbstractViewsList;

class PDFTemplateViewList extends AbstractViewsList
{
    /**
     * {@inheritDoc}
     */
    protected function getViewsList()
    {
        return array(
            new View(
                'ibnab.pmanager.pdftemplate.datagrid.template.view.system_templates',
                array(
                    'isSystem' => array(
                        'value' => 1,
                    ),
                )
            )
        );
    }
}
