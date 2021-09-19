<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use FOS\RestBundle\Controller\Annotations as Rest;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\Model\EndpointInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Call extends AbstractController
{
    public function __construct(private EndpointService $service)
    {
    }

    /**
     * @Rest\Get("/services/endpoints/call/{path}", name=Call::class, requirements={"path"=".+"})
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
     *
     * @param string $path
     *
     * @return Response
     */
    public function __invoke(Request $request, string $path): Response
    {
        $endpoint = $this->service->getByPath(sprintf('/%s', $path));
        if (!$endpoint instanceof EndpointInterface) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->service->call($endpoint, $request->query->all()));
    }
}
