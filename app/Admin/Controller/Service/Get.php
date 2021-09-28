<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Service;

use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Audit\Audit as Record;
use KejawenLab\ApiSkeleton\Audit\AuditService;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\ServiceInterface;
use KejawenLab\Application\Domain\ServiceService;
use KejawenLab\Application\Entity\Service;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="SERVICE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Get extends AbstractController
{
    public function __construct(private ServiceService $service, private AuditService $audit, private Reader $reader)
    {
        parent::__construct($this->service);
    }

    /**
     * @Route("/services/{id}", name=Get::class, methods={"GET"})
     */
    public function __invoke(string $id): Response
    {
        $service = $this->service->get($id);
        if (!$service instanceof ServiceInterface) {
            $this->addFlash('error', 'sas.page.service.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $audit = new Record($service);
        if ($this->reader->getProvider()->isAuditable(Service::class)) {
            $audit = $this->audit->getAudits($service, $id, 1);
        }

        return $this->renderAudit($audit, new ReflectionClass(Service::class));
    }
}
