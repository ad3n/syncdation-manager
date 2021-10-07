<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use KejawenLab\Application\Domain\ServiceService;
use KejawenLab\Application\Entity\Service;
use KejawenLab\Application\Form\DatabaseServiceType;
use KejawenLab\Application\Form\ElasticsearchServiceType;
use KejawenLab\Application\Form\ExcelServiceType;
use KejawenLab\Application\Form\FileServiceType;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="SERVICE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Main extends AbstractController
{
    public function __construct(private ServiceService $service, private Paginator $paginator)
    {
        parent::__construct($this->service, $paginator);
    }

    /**
     * @Route("/services", name=Main::class, methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request): Response
    {
        $service = new Service();
        if ($request->isMethod(Request::METHOD_POST)) {
            $id = $request->getSession()->get('id');
            if (null !== $id) {
                $service = $this->service->get($id);
            }
        } else {
            $flashes = $request->getSession()->getFlashBag()->get('id');
            foreach ($flashes as $flash) {
                $service = $this->service->get($flash);
                if (null !== $service) {
                    $request->getSession()->set('id', $service->getId());

                    break;
                }
            }
        }

        $elasticsearch = $this->createForm(ElasticsearchServiceType::class, $service);
        $database = $this->createForm(DatabaseServiceType::class, $service);
        $file = $this->createForm(FileServiceType::class, $service);
        $excel = $this->createForm(ExcelServiceType::class, $service);
        if ($request->isMethod(Request::METHOD_POST)) {
            /** @var FormInterface $form */
            $valid = false;
            foreach ([$elasticsearch, $database, $file, $excel] as $form) {
                $form->handleRequest($request);
                $service = $form->getData();
                if ('' === $service->getType()) {
                    continue;
                }

                $valid = true;
            }

            if (!$valid) {
                $this->addFlash('error', 'sas.page.service.not_saved');
            } else {
                /** @var Service $service */
                if (Service::TYPE_ELASTICSEARCH === $service->getType()) {
                    //Validate here
                }
                if (Service::TYPE_DATABASE === $service->getType()) {
                    //Validate here
                }
                if (Service::TYPE_FILE === $service->getType()) {
                    //Validate here
                }
                if (Service::TYPE_EXCEL === $service->getType()) {
                    //Validate here
                }

                $this->addFlash('info', 'sas.page.service.saved');

                $elasticsearch = $this->createForm(ElasticsearchServiceType::class);
                $database = $this->createForm(DatabaseServiceType::class);
                $file = $this->createForm(ElasticsearchServiceType::class);
                $excel = $this->createForm(ElasticsearchServiceType::class);
            }
        }

        $class = new ReflectionClass($service);
        $context = StringUtil::lowercase($class->getShortName());

        return $this->render(sprintf('%s/all.html.twig', $context), [
            'page_title' => sprintf('sas.page.%s.list', $context),
            'context' => $context,
            'properties' => $class->getProperties(ReflectionProperty::IS_PRIVATE),
            'paginator' => $this->paginator->paginate($this->service->getQueryBuilder(), $request, $class->getName()),
            'elasticsearch_form' => $elasticsearch->createView(),
            'database_form' => $database->createView(),
            'file_form' => $file->createView(),
            'excel_form' => $excel->createView(),
        ]);
    }
}
