<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\LicenseService;
use KejawenLab\Application\Entity\License;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="NODE", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Update extends AbstractController
{
    public function __construct(private LicenseService $licenseService)
    {
    }

    #[Route(path: '/services/nodes/update', name: Update::class, methods: ['GET'], priority: 255)]
    public function __invoke(Request $request): Response
    {
        $licenses = $this->licenseService->all();
        /** @var License $license */
        foreach ($licenses as $license) {
            $this->licenseService->add($license->getKey());
        }

        $this->addFlash('info', 'sas.page.node.updated');

        return new RedirectResponse($this->generateUrl(Main::class, array_merge($request->query->all())));
    }
}
