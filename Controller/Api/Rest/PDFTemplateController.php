<?php

namespace Ibnab\Bundle\PmanagerBundle\Controller\Api\Rest;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SecurityBundle\Authentication\Token\UsernamePasswordOrganizationToken;

use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;

use Ibnab\Bundle\PmanagerBundle\Provider\VariablesProvider;
use Ibnab\Bundle\PmanagerBundle\Entity\Repository\PDFTemplateRepository;
use Ibnab\Bundle\PmanagerBundle\Entity\PDFTemplate;

/**
 * @RouteResource("pdftemplate")
 * @NamePrefix("oro_api_")
 */
class PDFTemplateController extends RestController
{
    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete pdftemplate template",
     *      resource=true
     * )
     * @Acl(
     *      id="pmanager_template_delete",
     *      type="entity",
     *      class="IbnabPmanagerBundle:PDFTemplate",
     *      permission="DELETE"
     * )
     * @Delete(requirements={"id"="\d+"})
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $entity = $this->getManager()->find($id);
        if (!$entity) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        /**
         * Deny to remove system templates
         */
        if ($entity->getIsSystem()) {
            return $this->handleView($this->view(null, Codes::HTTP_FORBIDDEN));
        }

        $em = $this->getManager()->getObjectManager();
        $em->remove($entity);
        $em->flush();

        return $this->handleView($this->view(null, Codes::HTTP_NO_CONTENT));
    }

    /**
     * REST GET templates by entity name
     *
     * @param string $entityName
     *
     * @ApiDoc(
     *     description="Get templates by entity name",
     *     resource=true
     * )
     * @AclAncestor("pmanager_api_template_index")
     * @Get("/pdfltemplates/list/{entityName}",
     *      requirements={"entityName"="\w+"}
     * )
     *
     * @return Response
     */
    public function cgetAction($entityName = null)
    {
        if (!$entityName) {
            return $this->handleView(
                $this->view(null, Codes::HTTP_NOT_FOUND)
            );
        }

        $securityContext = $this->get('security.context');
        /** @var UsernamePasswordOrganizationToken $token */
        $token        = $securityContext->getToken();
        $organization = $token->getOrganizationContext();

        $entityName = $this->get('oro_entity.routing_helper')->decodeClassName($entityName);

        /** @var $emailTemplateRepository EmailTemplateRepository */
        $pdfTemplateRepository = $this->getDoctrine()->getRepository('IbnabPmanagerBundle:PDFTemplate');
        $templates               = $pdfTemplateRepository->getTemplateByEntityName($entityName, $organization);

        return $this->handleView(
            $this->view($templates, Codes::HTTP_OK)
        );
    }

    /**
     * REST GET available variables
     *
     * @ApiDoc(
     *     description="Get available variables",
     *     resource=true
     * )
     * @AclAncestor("pmanager_api_template_view")
     * @Get("/pdftemplates/variables")
     *
     * @return Response
     */
    public function getVariablesAction()
    {
        /** @var VariablesProvider $provider */
        $provider = $this->get('ibnab_pmanager.pdftemplate.variable_provider');

        $data = [
            'system' => $provider->getSystemVariableDefinitions(),
            'entity' => $provider->getEntityVariableDefinitions()
        ];

        return $this->handleView(
            $this->view($data, Codes::HTTP_OK)
        );
    }



    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('ibnab_pmanager.pdftemplate.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->get('ibnab_pmanager.form.type.pdftemplate.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->get('ibnab_pmanager.form.handler.pdftemplate.api');
    }
}
