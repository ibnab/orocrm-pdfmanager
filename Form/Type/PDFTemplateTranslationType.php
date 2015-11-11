<?php

namespace Ibnab\Bundle\PmanagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
class PDFTemplateTranslationType extends AbstractType
{
  
    protected $configManager;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }
  /**
     * Set labels for translation widget tabs
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['labels'] = $options['labels'];                   
    }
    /** @var ConfigManager */

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    $isWysiwygEnabled = $this->configManager->get('oro_form.wysiwyg_enabled');

        $resolver->setDefaults(
            [
                'translatable_class'   => 'Ibnab\\Bundle\\PmanagerBundle\\Entity\\PDFTemplate',
                'intention'            => 'pdftemplate_translation',
                'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
                'cascade_validation'   => true,
                'labels'               => [],
                'content_options'      => [],
                'fields'               => function (Options $options) use ($isWysiwygEnabled) {
                    return [
                        'content' => array_merge(
                            [
                                'field_type'      => 'oro_rich_text',
                                'attr'            => [
                                    'class'                => 'template-editor',
                                    'data-wysiwyg-enabled' => $isWysiwygEnabled,
                                ],
                                'wysiwyg_options' => [
                                    'height'     => '400px',
                                    'width'    => '100%'
                                ]
                            ],
                            $options->get('content_options')
                        )
                    ];
                },
            ]
        );
    }

    public function getParent()
    {
        return 'a2lix_translations_gedmo';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ibnab_pmanager_pdftemplate_translatation';
    }
}
