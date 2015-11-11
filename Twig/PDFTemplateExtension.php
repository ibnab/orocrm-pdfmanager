<?php

namespace Ibnab\Bundle\PmanagerBundle\Twig;

use Ibnab\Bundle\PmanagerBundle\Tools\PDFTemplateHolderHelper;

class PDFTemplateExtension extends \Twig_Extension
{
    const NAME = 'oro_pdftemplate';

    /** @var EmailHolderHelper */
    protected $emailHolderHelper;

    /**
     * @param EmailHolderHelper $emailHolderHelper
     */
    public function __construct(PDFTemplateHolderHelper $emailHolderHelper)
    {
        $this->emailHolderHelper = $emailHolderHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('oro_get_email', [$this, 'getEmail']),
        ];
    }

    /**
     * Gets the email address of the given object
     *
     * @param object $object
     * @return string The email address or empty string if the object has no email
     */
    public function getEmail($object)
    {
        $result = $this->emailHolderHelper->getEmail($object);

        return null !== $result
            ? $result
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
