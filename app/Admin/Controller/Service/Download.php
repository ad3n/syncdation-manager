<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Service;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Permission(menu="SERVICE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Download extends AbstractController
{
    public function __construct(private ServiceService $service, private SerializerInterface $serializer)
    {
    }

    /**
     * @Route("/services/download", name=Download::class, methods={"GET"})
     */
    public function __invoke(): Response
    {
        $records = $this->service->total();
        if (10000 < $records) {
            $this->addFlash('error', 'sas.page.error.too_many_records');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s_%s.csv"', 'services', date('YmdHis')));

        $response->setContent($this->serializer->serialize($this->service->all(), 'csv', ['groups' => 'read']));

        return $response;
    }
}
