<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use FOS\RestBundle\Controller\Annotations as Rest;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\Model\EndpointInterface;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Call extends AbstractController
{
    public function __construct(private EndpointService $service, private NodeService $nodeService)
    {
    }

    /**
     * @Rest\Get("/services/nodes/{nodeId}/endpoints/call/{path}", name=Call::class, requirements={"path"=".+"})
     *
     * @OA\Tag(name="Endpoint")
     * @OA\Response(
     *     response=200,
     *     description= "Endpoint detail",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object"
     *             )
     *         )
     *     }
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param string $nodeId
     * @param string $path
     *
     * @return Response
     */
    public function __invoke(Request $request, string $nodeId, string $path): Response
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            throw new NotFoundHttpException();
        }

        $endpoint = $this->service->getByNodeAndPath($node, sprintf('/%s', $path));
        if (!$endpoint instanceof EndpointInterface) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($this->service->call($endpoint, $request->query->all()));
    }
}
