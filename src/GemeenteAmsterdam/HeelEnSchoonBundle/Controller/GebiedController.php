<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class GebiedController extends Controller
{
    /**
     * @Route("/gebied")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function selectorAction(Request $request)
    {
        $gebieden = $this->getDoctrine()->getRepository(Gebied::class)->findAll();

        return $this->render('HeelEnSchoonBundle:Gebied:selector.html.twig', [
            'gebieden' => $gebieden
        ]);
    }
}