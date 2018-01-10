<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RedirectController extends Controller
{
    /**
     * @Route(
     *  "/{url}",
     *  name="remove_trailing_slash",
     *  requirements={"url" = ".*\/$"}, methods={"GET"}
     * )
     */
    public function removeTrailingSlashAction(Request $request, $url)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }

    /**
     * @Route("/app")
     */
    public function legacyUrlRedirectAction()
    {
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_leegnu_index');
    }

    /**
     * @Route("/conciergeapp")
     * @Route("/tickets")
     */
    public function legacyAppUrlRedirectAction()
    {
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_gebied_selector');
    }


}