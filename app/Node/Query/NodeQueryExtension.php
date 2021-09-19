<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node\Query;

use Doctrine\ORM\QueryBuilder;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\ApiSkeleton\Pagination\Query\AbstractQueryExtension;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeQueryExtension extends AbstractQueryExtension
{
    public function apply(QueryBuilder $queryBuilder, Request $request): void
    {
        $query = $request->query->get('q');
        if (!$query) {
            return;
        }

        $queryBuilder->andWhere($queryBuilder->expr()->like(sprintf('UPPER(%s.code)', $this->aliasHelper->findAlias('root')), $queryBuilder->expr()->literal(sprintf('%%%s%%', StringUtil::uppercase($query)))));
        $queryBuilder->andWhere($queryBuilder->expr()->like(sprintf('UPPER(%s.name)', $this->aliasHelper->findAlias('root')), $queryBuilder->expr()->literal(sprintf('%%%s%%', StringUtil::uppercase($query)))));
    }

    public function support(string $class, Request $request): bool
    {
        return in_array(NodeInterface::class, class_implements($class));
    }
}
