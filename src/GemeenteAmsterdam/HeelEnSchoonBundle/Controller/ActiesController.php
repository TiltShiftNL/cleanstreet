<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\BelActie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\NotitieActie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\NotitieActieFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Actie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\FotoActie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\FotoActieFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\StatusActie;
use Symfony\Component\HttpFoundation\Request;

class ActiesController extends Controller
{
    /**
     * @Route("/tickets/{gebiedId}/{ticketId}/acties/nieuw/bellen")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket", options={"id"="ticketId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createBelAction(Request $request, Ticket $ticket, Gebied $gebied)
    {
        $telefoonboekEntry = $this->getDoctrine()->getManager()->getRepository(TelefoonboekEntry::class)->find($request->request->get('telefoonboekEntry'));
        if ($telefoonboekEntry === null) {
            throw $this->createNotFoundException('Telefoon boek entry not found');
        }

        $belActie = new BelActie();
        $belActie->setMedewerker($this->getUser());
        $belActie->setTelefoonboekEntry($telefoonboekEntry);
        $belActie->setTicket($ticket);

        $this->getDoctrine()->getManager()->persist($belActie);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_detail', ['gebiedId' => $gebied->getId(), 'ticketId' => $ticket->getId()]);
    }

    /**
     * @Route("/tickets/{gebiedId}/{ticketId}/acties/nieuw/notitie")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket", options={"id"="ticketId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createNotitieAction(Request $request, Ticket $ticket, Gebied $gebied)
    {
        $actie = new NotitieActie();

        $form = $this->createForm(NotitieActieFormType::class, $actie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $actie->setMedewerker($this->getUser());
            $actie->setTicket($ticket);
            $this->getDoctrine()->getManager()->persist($actie);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_detail', ['gebiedId' => $gebied->getId(), 'ticketId' => $ticket->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/tickets/{gebiedId}/{ticketId}/acties/nieuw/foto")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket", options={"id"="ticketId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createFotoAction(Request $request, Ticket $ticket, Gebied $gebied)
    {
        $actie = new FotoActie();

        $form = $this->createForm(FotoActieFormType::class, $actie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $actie->setMedewerker($this->getUser());
            $actie->setTicket($ticket);
            $this->getDoctrine()->getManager()->persist($actie);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_detail', ['gebiedId' => $gebied->getId(), 'ticketId' => $ticket->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/tickets/{gebiedId}/{ticketId}/acties/{actieId}/verwijderen")
     * @Method("POST")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket", options={"id"="ticketId"})
     * @ParamConverter("actie", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Actie", options={"id"="actieId"})
     */
    public function removeAction(Request $request, Ticket $ticket, Actie $actie, Gebied $gebied)
    {
        if ($actie->getTicket() !== $ticket) {
            throw $this->createNotFoundException('Actie/Ticket mismatch');
        }
        if ($actie instanceof StatusActie) {
            throw $this->createAccessDeniedException();
        }
        $actie->setVerwijderd(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_detail', ['gebiedId' => $gebied->getId(), 'ticketId' => $ticket->getId()]);
    }
}