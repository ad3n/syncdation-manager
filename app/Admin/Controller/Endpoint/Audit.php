<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Audit\AuditService;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Admin\Controller\Node\Main as NodeMain;
use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="AUDIT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Audit extends AbstractController
{
    public function __construct(
        private EndpointService $service,
        private NodeService $nodeService,
        private AuditService $audit,
        private Reader $reader
    ) {
        parent::__construct($this->service);
    }

    #[Route(path: '/services/nodes/{nodeId}/endpoints/{id}/audit', name: Audit::class, methods: ['GET'], priority: -255)]
    public function __invoke(Request $request, string $nodeId, string $id): Response
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(NodeMain::class));
        }

        if (!$entity = $this->service->get($id)) {
            $this->addFlash('error', 'sas.page.endpoint.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, array_merge($request->query->all(), ['nodeId' => $nodeId])));
        }

        if (!$this->reader->getProvider()->isAuditable(Endpoint::class)) {
            $this->addFlash('error', 'sas.page.audit.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, array_merge($request->query->all(), ['nodeId' => $nodeId])));
        }

        return $this->renderAudit($this->audit->getAudits($entity, $id), new ReflectionClass(Endpoint::class));
    }
}
