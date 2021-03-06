<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Query;

use Doctrine\ORM\QueryBuilder;
use KejawenLab\ApiSkeleton\Pagination\Query\AbstractQueryExtension;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class EndpointQueryExtension extends AbstractQueryExtension
{
    public function apply(QueryBuilder $queryBuilder, Request $request): void
    {
        $query = $request->query->get('q');
        if (!$query) {
            return;
        }

        $queryBuilder->andWhere($queryBuilder->expr()->like(sprintf('%s.path', $this->aliasHelper->findAlias('root')), $queryBuilder->expr()->literal(sprintf('%%%s%%', StringUtil::lowercase($query)))));

    }

    public function support(string $class, Request $request): bool
    {
        return in_array(EndpointInterface::class, class_implements($class));
    }
}
