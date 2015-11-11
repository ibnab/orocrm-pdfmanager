<?php

namespace Ibnab\Bundle\PmanagerBundle\Model;

/**
 * Represents an email message template
 */
interface PDFTemplateInterface
{
    /**
     * Gets email template type
     *
     * @return string
     */
    public function getType();


    /**
     * Gets email template content
     *
     * @return string
     */
    public function getContent();

    /**
     * Sets email template content
     *
     * @param string $content
     *
     * @return EmailTemplateInterface
     */
    public function setContent($content);

}
