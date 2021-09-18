<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Audit\Audit as Record;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use KejawenLab\ApiSkeleton\Audit\AuditService;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="NODE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Get extends AbstractController
{
    public function __construct(private NodeService $service, private AuditService $audit, private Reader $reader)
    {
        parent::__construct($this->service);
    }

    /**
     * @Route("/services/nodes/{id}", name=Get::class, methods={"GET"})
     */
    public function __invoke(string $id): Response
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $audit = new Record($node);
        if ($this->reader->getProvider()->isAuditable(Node::class)) {
            $audit = $this->audit->getAudits($node, $id, 1);
        }

        return $this->renderAudit($audit, new ReflectionClass(Node::class));
    }
}
