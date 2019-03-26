<?php
/**
 * Created by PhpStorm.
 * User: guagua.lai
 * Date: 2019/3/22
 * Time: 下午 06:52
 */

namespace App\Controller;

use Omines\DataTablesBundle\DataTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Omines\DataTablesBundle\DataTableFactory;

use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;

use App\Entity\Word;

class FrontendController extends AbstractController
{

    use DataTablesTrait;
    protected $datatable;

    public function __construct(DataTableFactory $dataTableFactory)
    {
        $this->datatable = $dataTableFactory;
    }

    /**
     * @Route("/value-list")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function ListAction(Request $request)
    {

        $wordRepository = $this->getDoctrine()->getRepository(Word::class);
        $table = $wordRepository->findAll();

//        $table =  $this->datatable->create()
////            ->add('value', TextColumn::class)
////            ->add('from', TextColumn::class)
//            ->createAdapter(ORMAdapter::class, [
//                'entity' => Word::class,
//            ]);

        return $this->render('Frontend/list.html.twig', ['datatable' => $table]);
    }
}
