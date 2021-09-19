<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Node;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="NODE", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractFOSRestController
{
    public function __construct(private NodeService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Delete("/services/nodes/{id}", name=Delete::class)
     *
     * @OA\Tag(name="Node")
     * @OA\Response(
     *     response=204,
     *     description="Node deleted"
     * )
     *
     * @Security(name="Bearer")
     *
     * @param string $id
     *
     * @return View
     */
    public function __invoke(string $id): View
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.node.not_found', [], 'pages'));
        }

        $this->service->remove($node);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
