<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Query;

use Doctrine\ORM\QueryBuilder;
use KejawenLab\ApiSkeleton\Pagination\Query\AbstractQueryExtension;
use KejawenLab\Application\Entity\Service;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class ServiceQueryExtension extends AbstractQueryExtension
{
    public function apply(QueryBuilder $queryBuilder, Request $request): void
    {
        $query = $request->query->get('q');
        if (!$query) {
            return;
        }

        /**
         * Uncomment to implement your own search logic
         *
         * $queryBuilder->andWhere($queryBuilder->expr()->like(sprintf('UPPER(%s.name)', $this->aliasHelper->findAlias('root')), $queryBuilder->expr()->literal(sprintf('%%%s%%', StringUtil::uppercase($query)))));
         */
    }

    public function support(string $class, Request $request): bool
    {
        return $class === Service::class;
    }
}
