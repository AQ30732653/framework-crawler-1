<?php

namespace App\Repository;

use App\Entity\Word;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Word|null find($id, $lockMode = null, $lockVersion = null)
 * @method Word|null findOneBy(array $criteria, array $orderBy = null)
 * @method Word[]    findAll()
 * @method Word[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Word::class);
    }

    public function findOneByValueAndFrom($value, $from)
    {
        $model = $this->createQueryBuilder('w')
            ->andWhere('w.value = :val')
            ->andWhere('w.from = :from')
            ->setParameter('val', $value)
            ->setParameter('from', $from)
            ->getQuery()
            ->getOneOrNullResult();
        unset($value);
        unset($from);

        return $model;
    }

    public function createOrUpdate($array)
    {
        $entityManager = $this->getEntityManager();

        try {
            $word = $this->findOneByValueAndFrom($array['value'], $array['from']);

            if (!empty($word)) {
                $counts = $word->getCounts() + 1;
                $word->setCounts($counts);
                $entityManager->flush();
            } else {
                $word = new Word();
                $word->setCounts(1);
                $word->setValue($array['value']);
                $word->setFrom($array['from']);
                $entityManager->persist($word);
                $entityManager->flush();
            }
            unset($word);
        } catch (\Exception $exception) {
            var_dump('記憶體使用：'.memory_get_usage());
            var_dump($exception->getMessage());exit;
        }

        return true;
    }
}
