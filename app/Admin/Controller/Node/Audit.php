<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Node\NodeService;
use KejawenLab\ApiSkeleton\Audit\AuditService;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="AUDIT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Audit extends AbstractController
{
    public function __construct(private NodeService $service, private AuditService $audit, private Reader $reader)
    {
        parent::__construct($this->service);
    }

    #[Route(path: '/services/nodes{id}/audit', name: Audit::class, methods: ['GET'], priority: -255)]
    public function __invoke(string $id): Response
    {
        if (!$entity = $this->service->get($id)) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        if (!$this->reader->getProvider()->isAuditable(Node::class)) {
            $this->addFlash('error', 'sas.page.audit.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        return $this->renderAudit($this->audit->getAudits($entity, $id), new ReflectionClass(Node::class));
    }
}
