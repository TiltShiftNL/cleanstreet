<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use MessageBird\Client;
use MessageBird\Objects\VoiceMessage;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\LedigingsVerzoek;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;

class LeegNuListener
{
    /**
     * @var Client
     */
    protected $messageBird;

    /**
     * @var boolean
     */
    protected $phoneEnabled;

    public function __construct($messageBird, $phoneEnabled)
    {
        $this->messageBird = $messageBird;
        $this->phoneEnabled = $phoneEnabled;
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!($entity instanceof LedigingsVerzoek)) {
            return;
        }

        if ($entity->getBron() !== Ticket::BRON_LEEGNU) {
            return;
        }

        $this->doVoiceCall($event);
    }

    public function doVoiceCall(LifecycleEventArgs $event)
    {
        if ($this->phoneEnabled === null || $this->phoneEnabled == false) {
            return;
        }

        /** @var $entity Melding */
        $entity = $event->getEntity();

        $message = new VoiceMessage();
        $message->recipients = [$entity->getOnderneming()->getGebied()->getTelefoon()];
        $message->body = 'Hallo concierge. De bak van ' . $entity->getOndernemersBak()->getOnderneming()->getNaam() . ' in de straat ' . $entity->getOndernemersBak()->getOnderneming()->getStraat() . ' moet worden geleegd. Het nummer van de bak is <prosody rate="-40%">' . implode(', ', str_split($entity->getOndernemersBak()->getKenmerk())) . '</prosody>. Ik herhaal <prosody rate="-40%">' . implode(', ', str_split($entity->getOndernemersBak()->getKenmerk())). '</prosody>. Een fijne dag toegewenst van leeg punt nu.<break time="3s"/>';
        $message->language = 'nl-nl';
        $message->voice = 'male';
        $message->repeat = 3;
        $message->ifMachine = 'continue';

        $this->messageBird->voicemessages->create($message);
    }
}