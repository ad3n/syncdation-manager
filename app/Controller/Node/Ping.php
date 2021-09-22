<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Node;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="NODE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Ping extends AbstractFOSRestController
{
    public function __construct(private NodeService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Get("/services/nodes/{id}/ping", name=Ping::class)
     *
     * @OA\Tag(name="Node")
     * @OA\Response(
     *     response=200,
     *     description= "Node detail",
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
     * @param string $id
     */
    public function __invoke(string $id): Response
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.node.not_found', [], 'pages'));
        }

        if ($this->service->ping($node)) {
            return new JsonResponse(null);
        }

        return new JsonResponse(null, Response::HTTP_GONE);
    }
}
