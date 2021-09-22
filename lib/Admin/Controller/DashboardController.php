<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Admin\Controller;

use KejawenLab\ApiSkeleton\Admin\AdminContext;
use KejawenLab\Application\Domain\LicenseService;
use KejawenLab\Application\Entity\License;
use KejawenLab\Application\Form\LicenseType;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\NodeService;
use KejawenLab\Application\Repository\LicenseRepository;
use KejawenLab\Application\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Base;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class DashboardController extends Base
{
    public function __construct(
        private NodeService $nodeService,
        private ServiceRepository $serviceRepository,
        private LicenseService $licenseService,
        private EndpointService $endpointService,
    ) {
    }

    #[Route(path: '/', name: AdminContext::ADMIN_ROUTE, methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(LicenseType::class);
        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var License $data */
                $data = $form->getData();

                if ($this->licenseService->add($data->getKey())) {
                    $this->addFlash('info', 'sas.page.license.saved');
                } else {
                    $this->addFlash('error', 'sas.page.license.not_saved');
                }
            }
        }

        return $this->render('dashboard/layout.html.twig', [
            'page_title' => 'sas.page.dashboard',
            'license' => $this->licenseService->total(),
            'form' => $form->createView(),
            'node' => $this->nodeService->total(),
            'service' => count($this->serviceRepository->findAll()),
            'endpoint' => $this->endpointService->total(),
            'uptime' => $this->nodeService->calculateUptime(),
        ]);
    }
}
