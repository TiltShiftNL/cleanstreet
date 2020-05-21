<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;

class WebsiteController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $fb = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_website_index', ['_fragment' => 'contact'])
        ]);
        $fb->add('gebied', EntityType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank()
            ],
            'class' => Gebied::class,
            'choice_label' => function (Gebied $gebied) {
                return $gebied->getNaam();
            }
        ]);
        $fb->add('naam', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1, 'max' => 255])
            ]
        ]);
        $fb->add('email', EmailType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Email()
            ]
        ]);
        $fb->add('telefoon', TextType::class, [
            'required' => false,
            'constraints' => [
                new Length(['min' => 0, 'max' => 20])
            ]
        ]);
        $fb->add('postcode', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1, 'max' => 10])
            ]
        ]);
        $fb->add('vraag', TextareaType::class, [
            'required' => false,
            'constraints' => [
                new Length(['min' => 0, 'max' => 4096])
            ]
        ]);
        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notitie = new Notitie();
            $notitie->setGebied($form->get('gebied')->getData());
            $notitie->setBron(Notitie::BRON_CLEANSTREET);
            $notitie->setTekst(
                'Contactformulier via ' . $_SERVER['REQUEST_URI'] . PHP_EOL .
                'Naam: ' . $form->get('naam')->getData() . PHP_EOL .
                'E-mail: ' . $form->get('email')->getData() . PHP_EOL .
                'Telefoon: ' . $form->get('telefoon')->getData() . PHP_EOL .
                'Postcode: ' . $form->get('postcode')->getData() . PHP_EOL .
                'Vraag: ' . $form->get('vraag')->getData()
            );
            $this->getDoctrine()->getManager()->persist($notitie);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_website_contact');
        }
        return $this->render('HeelEnSchoonBundle:Website:index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contact")
     */
    public function contactAction(Request $request)
    {
        return $this->render('HeelEnSchoonBundle:Website:contact.html.twig');
    }

    /**
     * @Route("/about")
     */
    public function aboutAction(Request $request)
    {
        return $this->render('HeelEnSchoonBundle:Website:about.html.twig');
    }
}