<?php

namespace Ibnab\Bundle\PmanagerBundle\Provider;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use Symfony\Component\Translation\TranslatorInterface;

use Doctrine\Common\Cache\Cache;

use Ibnab\Bundle\PmanagerBundle\Entity\PDFTemplate;
use Ibnab\Bundle\PmanagerBundle\Model\PDFTemplateInterface;

class PDFTemplateRenderer extends \Twig_Environment
{
    const VARIABLE_NOT_FOUND = 'ibnab.pmanager.variable.not.found';

    /** @var VariablesProvider */
    protected $variablesProvider;

    /** @var  Cache|null */
    protected $sandBoxConfigCache;

    /** @var  string */
    protected $cacheKey;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var PropertyAccessor */
    protected $accessor;

    /**
     * @param \Twig_LoaderInterface   $loader
     * @param array                   $options
     * @param VariablesProvider       $variablesProvider
     * @param Cache                   $cache
     * @param                         $cacheKey
     * @param \Twig_Extension_Sandbox $sandbox
     * @param TranslatorInterface     $translator
     */
    public function __construct(
        \Twig_LoaderInterface $loader,
        $options,
        VariablesProvider $variablesProvider,
        Cache $cache,
        $cacheKey,
        \Twig_Extension_Sandbox $sandbox,
        TranslatorInterface $translator
    ) {
        parent::__construct($loader, $options);

        $this->variablesProvider  = $variablesProvider;
        $this->sandBoxConfigCache = $cache;
        $this->cacheKey           = $cacheKey;

        $this->addExtension($sandbox);
        $this->configureSandbox();

        $this->translator = $translator;
    }

    /**
     * Configure sandbox form config data
     *
     */
    protected function configureSandbox()
    {
        $allowedData = $this->sandBoxConfigCache->fetch($this->cacheKey);
        
        if (false === $allowedData) {
            $allowedData = $this->prepareConfiguration();
            $this->sandBoxConfigCache->save($this->cacheKey, serialize($allowedData));
        } else {
            $allowedData = unserialize($allowedData);
        }

        /** @var \Twig_Extension_Sandbox $sandbox */
        $sandbox = $this->getExtension('sandbox');
        /** @var \Twig_Sandbox_SecurityPolicy $security */
        $security = $sandbox->getSecurityPolicy();
        if(isset($allowedData['properties']) && isset($allowedData['methods']))
        {
        $security->setAllowedProperties($allowedData['properties']);
        $security->setAllowedMethods($allowedData['methods']);
        }
    }

    /**
     * Prepare configuration from entity config
     *
     * @return array
     */
    private function prepareConfiguration()
    {
        $configuration = array();

        $allGetters = $this->variablesProvider->getEntityVariableGetters();
        foreach ($allGetters as $className => $getters) {
            $properties = [];
            $methods    = [];
            foreach ($getters as $varName => $getter) {
                if (empty($getter)) {
                    $properties[] = $varName;
                } else {
                    $methods[] = $getter;
                }
            }

            $configuration['properties'][$className] = $properties;
            $configuration['methods'][$className]    = $methods;
        }

        return $configuration;
    }

    /**
     * Compile email message
     *
     * @param EmailTemplateInterface $template
     * @param array                  $templateParams
     *
     * @return array first element is email subject, second - message
     */
    public function compileMessage(PDFTemplateInterface $template, array $templateParams = array())
    {
        $templateParams['system'] = $this->variablesProvider->getSystemVariableValues();

        $content = $template->getContent();

        if (isset($templateParams['entity'])) {
            $content = $this->processDateTimeVariables($content, $templateParams['entity']);
        }

        $templateRendered = $this->render($content, $templateParams);


        return array(null, $templateRendered);
    }

    /**
     * Compile preview content
     *
     * @param EmailTemplate $entity
     *
     * @return string
     */
    public function compilePreview(PDFTemplate $entity)
    {
        return $this->render('{% verbatim %}' . $entity->getContent() . '{% endverbatim %}', []);
    }

    /**
     * Process entity variables of dateTime type
     *   -- datetime variables with formatting will be skipped, e.g. {{ entity.createdAt|date('F j, Y, g:i A') }}
     *   -- processes ONLY variables that passed without formatting, e.g. {{ entity.createdAt }}
     *
     * Note:
     *  - add oro_format_datetime filter to all items which implement \DateTimeInterface
     *  - if value does not exists and PropertyAccess::getValue throw an error
     *    it will change on self::VARIABLE_NOT_FOUND
     *  - all tags that do not start with `entity` will be ignored
     *
     * TODO find a common way for processing formatter
     * @param string $template
     * @param object $entity
     *
     * @return EmailTemplate
     */
    protected function processDateTimeVariables($template, $entity)
    {
        $that     = $this;
        $callback = function ($match) use ($entity, $that) {
            $result = $match[0];
            $path   = $match[1];
            $split  = explode('.', $path);

            if ($split[0] && 'entity' === $split[0]) {
                unset($split[0]);

                try {
                    $value = $that->getValue($entity, implode('.', $split));

                    if ($value instanceof \DateTime || $value instanceof \DateTimeInterface) {
                        $result = sprintf('{{ %s|oro_format_datetime }}', $path);
                    }
                } catch (\Exception $e) {
                    $result = $that->translator->trans(self::VARIABLE_NOT_FOUND);
                }
            }

            return $result;
        };

        return preg_replace_callback('/{{\s([\w\d\.\_\-]*?)\s}}/u', $callback, $template);
    }

    /**
     * @param Object $entity
     * @param string $path
     *
     * @return mixed
     */
    protected function getValue($entity, $path)
    {
        $propertyAccess = $this->getPropertyAccess();

        return $propertyAccess->getValue($entity, $path);
    }

    /**
     * @return PropertyAccessor
     */
    protected function getPropertyAccess()
    {
        if (!$this->accessor instanceof PropertyAccessor) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->accessor;
    }
}
