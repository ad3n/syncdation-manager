<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\Model\EndpointInterface;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Get extends AbstractFOSRestController
{
    public function __construct(private EndpointService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Get("/endpoints/{id}", name=Get::class)
     *
     * @OA\Tag(name="Endpoint")
     * @OA\Response(
     *     response=200,
     *     description= "Endpoint detail",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 ref=@Model(type=Endpoint::class, groups={"read"})
     *             )
     *         )
     *     }
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param string $id
     *
     * @return View
     */
    public function __invoke(string $id): View
    {
        $endpoint = $this->service->get($id);
        if (!$endpoint instanceof EndpointInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.endpoint.not_found', [], 'pages'));
        }

        return $this->view($endpoint);
    }
}
