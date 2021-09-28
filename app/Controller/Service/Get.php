<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Service;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\ServiceInterface;
use KejawenLab\Application\Domain\ServiceService;
use KejawenLab\Application\Entity\Service;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="SERVICE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Get extends AbstractFOSRestController
{
    public function __construct(private ServiceService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Get("/services/{id}", name=Get::class)
     *
     * @OA\Tag(name="Service")
     * @OA\Response(
     *     response=200,
     *     description= "Service detail",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 ref=@Model(type=Service::class, groups={"read"})
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
        $service = $this->service->get($id);
        if (!$service instanceof ServiceInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.service.not_found', [], 'pages'));
        }

        return $this->view($service);
    }
}
