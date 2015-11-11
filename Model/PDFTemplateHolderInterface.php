<?php

namespace Ibnab\Bundle\PmanagerBundle\Model;

/**
 * Represents an subject which may receive email messages
 */
interface PDFTemplateHolderInterface
{
    /**
     * Gets an email address which can be used to send messages
     *
     * @return string
     */
    public function getEmail();
}
