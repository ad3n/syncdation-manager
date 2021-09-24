<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use KejawenLab\Application\Entity\Endpoint;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class GetAll extends AbstractFOSRestController
{
    public function __construct(private EndpointService $service, private NodeService $nodeService, private Paginator $paginator)
    {
    }

    /**
     * @Rest\Get("/services/nodes/{nodeId}/endpoints", name=GetAll::class)
     *
     * @OA\Tag(name="Endpoint")
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
     *     description= "Endpoint list",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=Endpoint::class, groups={"read"}))
     *             )
     *         )
     *     }
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param string|null $nodeId
     * @return View
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request, ?string $nodeId = "-nodeId-"): View
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            throw new NotFoundHttpException();
        }

        return $this->view($this->paginator->paginate($this->service->getQueryBuilder(), $request, Endpoint::class));
    }
}
