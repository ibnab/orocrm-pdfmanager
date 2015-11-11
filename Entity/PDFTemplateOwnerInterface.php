<?php

namespace Ibnab\Bundle\PmanagerBundle\Entity;

use Oro\Bundle\LocaleBundle\Model\FirstNameInterface;
use Oro\Bundle\LocaleBundle\Model\LastNameInterface;

/**
 * Represents an pdftempalte owner
 */
interface PDFTemplateOwnerInterface extends FirstNameInterface, LastNameInterface
{
    /**
     * Get entity class name.
     * TODO: This is a temporary solution for get 'view' route in twig.
     *       Will be removed after EntityConfigBundle is finished
     *
     * @return string
     */
    public function getClass();

    /**
     * Get names of fields contain pdftempalte addresses
     *
     * @return string[]|null
     */
    public function getPDFTemplateFields();

    /**
     * Get entity unique id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName();
}
