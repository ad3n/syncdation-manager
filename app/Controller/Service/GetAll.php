<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Service;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\ServiceService;
use KejawenLab\Application\Entity\Service;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Permission(menu="SERVICE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class GetAll extends AbstractFOSRestController
{
    public function __construct(private ServiceService $service, private Paginator $paginator)
    {
    }

    /**
     * @Rest\Get("/services", name=GetAll::class)
     *
     * @OA\Tag(name="Service")
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     @OA\Schema(
     *         type="integer",
     *         format="int32"
     *     )
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     @OA\Schema(
     *         type="integer",
     *         format="int32"
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description= "Service list",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=Service::class, groups={"read"}))
     *             )
     *         )
     *     }
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     *
     * @return View
     */
    public function __invoke(Request $request): View
    {
        return $this->view($this->paginator->paginate($this->service->getQueryBuilder(), $request, Service::class));
    }
}
