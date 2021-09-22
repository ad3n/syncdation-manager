<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractFOSRestController
{
    public function __construct(private EndpointService $service, private NodeService $nodeService, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Delete("/services/nodes/{nodeId}/endpoints/{id}", name=Delete::class)
     *
     * @OA\Tag(name="Endpoint")
     * @OA\Response(
     *     response=204,
     *     description="Endpoint deleted"
     * )
     *
     * @Security(name="Bearer")
     *
     * @param string $nodeId
     * @param string $id
     *
     * @return View
     */
    public function __invoke(string $nodeId, string $id): View
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            throw new NotFoundHttpException();
        }

        $endpoint = $this->service->get($id);
        if (!$endpoint instanceof EndpointInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.endpoint.not_found', [], 'pages'));
        }

        $this->service->remove($endpoint);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
