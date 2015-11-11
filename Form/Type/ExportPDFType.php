<?php

namespace Ibnab\Bundle\PmanagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Ibnab\Bundle\PmanagerBundle\Processor\ProcessorRegistry;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Ibnab\Bundle\PmanagerBundle\Entity\PDFTemplate as PDFTemplateEntity;
use Ibnab\Bundle\PmanagerBundle\Entity\Repository\PDFTemplateRepository;
use Oro\Bundle\SecurityBundle\Authentication\Token\UsernamePasswordOrganizationToken;
use Symfony\Component\HttpFoundation\RequestStack;

class ExportPDFType extends AbstractType
{
    const NAME = 'ibnab_pmanager_exportpdf';

    protected $securityContext;
    protected $requestStack;

    /**
     * @param ProcessorRegistry $processorRegistry
     */
    public function __construct(SecurityContextInterface $securityContext,RequestStack $requestStack)
    {
       $this->securityContext = $securityContext;
       $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('entityClass', 'hidden', ['required' => true])
       ->add('entityId', 'hidden', ['required' => true]);       
        $builder
            ->add(
                'template',
                'ibnab_pmanager_pdftemplate_list',
                [
                    'label' => 'oro.email.template.label',
                    'required' => true,
                    'depends_on_parent_field' => 'entityClass',
                    'configs' => [
                        'allowClear' => true
                    ]
                ]
            );
            $builder->add(
                'process',
                'choice',
                [
                    'label'      => 'oro.email.type.label',
                    'required'   => true,
                    'data'       => 'download',
                    'choices'  => [
                        'download' => 'ibnab.pmanager.pdftemplate.exportpdf.download_type',
                        'attach'  => 'ibnab.pmanager.pdftemplate.exportpdf.attach_type'
                    ],
                    'expanded'   => true
                ]
            );


        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'initChoicesByEntityName']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'initChoicesByEntityName']);


    }
/**
     * @param FormEvent $event
     */
    public function initChoicesByEntityName(FormEvent $event)
    {
        
        $valuefrompost = $this->requestStack->getCurrentRequest()->get('ibnab_pmanager_exportpdf');
        if($valuefrompost and isset($valuefrompost['entityClass'])){
        $entityClass = $valuefrompost['entityClass'];
        }
        else{
        $entityClass = $this->requestStack->getCurrentRequest()->get('entityClass');
        $entityClass = trim(str_replace("_","\\",$entityClass));
        }
        //$data = $event->getData();
        if (null === $entityClass){
            return;
        }

        //$entityClass = is_object($data) ? $data->getEntityClass() : $data['entityClass'];
        $form = $event->getForm();
        /** @var UsernamePasswordOrganizationToken $token */
        $token        = $this->securityContext->getToken();
        $organization = $token->getOrganizationContext();

        FormUtils::replaceField(
            $form,
            'template',
            [
                'selectedEntity' => $entityClass,
                'query_builder'  =>
                    function (PDFTemplateRepository $templateRepository) use (
                        $entityClass,
                        $organization
                    ) {
                        return $templateRepository->getEntityTemplatesQueryBuilder($entityClass, $organization, true);
                    },
            ],
            ['choice_list', 'choices']
        );

     /*   if ($this->securityContext->isGranted('EDIT', 'entity:Oro\Bundle\EmailBundle\Entity\EmailUser')) {
            FormUtils::replaceField(
                $form,
                'contexts',
                [
                    'read_only' => false,
                ]
            );
        }*/
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
