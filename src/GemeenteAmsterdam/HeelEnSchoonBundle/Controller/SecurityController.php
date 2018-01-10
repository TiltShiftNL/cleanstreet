<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker;

class SecurityController extends Controller
{
    /**
     * @Route("/login")
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser() !== null && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index');
        }

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $medewerkers = $this->getDoctrine()->getManager()->getRepository(Medewerker::class)->findAll();

        return $this->render('HeelEnSchoonBundle:Security:login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'medewerkers' => $medewerkers
        ]);
    }

    /**
     * @Route("/logout")
     */
    public function logoutAction()
    {
        //
    }
}