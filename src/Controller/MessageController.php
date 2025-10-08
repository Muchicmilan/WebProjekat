<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Entity\MessageRequest;
use App\Entity\User;
use App\Form\MessageRequestType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/message')]
class MessageController extends AbstractController {
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    //Korisnik salje poruke treneru ukoliko je nesto potrebno
    #[Route('/send', name:'app_send_msg' )]
    #[IsGranted('ROLE_USER')]
    public function sendMessage(Request $req, EntityManagerInterface $em)
    {
        /** @var \App\Entity\User $sender */
        $sender = $this->getUser();
        $this->logger->info('Korisnik {user} pokušava da pošalje poruku.', ['user' => $sender->getUserIdentifier()]);

        $msg = new MessageRequest();
        $form = $this->createForm(MessageRequestType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setDateSent(new DateTimeImmutable());
            $msg->setTimeSent(new DateTimeImmutable());
            $formData = $form->getData();
            $receiverMail = $formData['email'];
            $receiver = $em->getRepository(User::class)->findOneBy(['email' => $receiverMail]);

            if (!$receiver || $receiver->getRole() === UserRole::USER) {
                $this->logger->warning('Slanje poruke neuspešno. Korisnik {sender} je pokušao da pošalje poruku primaocu koji nije trener/admin: {receiver}.', [
                    'sender' => $sender->getUserIdentifier(),
                    'receiver' => $receiverMail
                ]);
                $this->addFlash('warning', 'Korisnik kome zelite poslati zahtev nije trener ili admin');
                return $this->redirectToRoute('app_send_msg');
            }

            $msg->setSender($sender);
            $msg->setReceiver($receiver);
            $msg->setContent($formData['content']);
            $em->persist($msg);
            $em->flush();

            $this->logger->info('Poruka uspešno poslata od {sender} ka {receiver}.', [
                'sender' => $sender->getUserIdentifier(),
                'receiver' => $receiver->getUserIdentifier()
            ]);

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('message/user_send_form.html.twig', [
            'messageForm' => $form
        ]);
    }
    #[Route('/trainer_inbox', name:'app_inbox' )]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function trainerInbox(EntityManagerInterface $em){
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->logger->info('Trener/Admin {user} pregleda svoje prijemno sanduče (inbox).', ['user' => $user->getUserIdentifier()]);

        $messages = $em
            ->getRepository(MessageRequest::class)
            ->findBy(
                ['receiver' => $user],
                ['date_sent' => 'DESC', 'time_sent' => 'DESC']
            );

        return $this->render('message/list_messages.html.twig', [
            'messages' => $messages,
            'page_title' => 'Primljene Poruke'
        ]);
    }

    #[Route('/sent', name: 'app_user_sent_msg')]
    #[IsGranted('ROLE_USER')]
    public function userSentMessages(EntityManagerInterface $em)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->logger->info('Korisnik {user} pregleda svoje poslate poruke.', ['user' => $user->getUserIdentifier()]);

        $messages = $em->getRepository(MessageRequest::class)->findBy(
            ['sender' => $user],
            ['date_sent' => 'DESC', 'time_sent' => 'DESC']
        );

        return $this->render('message/list_messages.html.twig', [
            'messages' => $messages,
            'page_title' => 'Poslate Poruke'
        ]);
    }

    #[Route('/respond/user/{id}', name:'app_trainer_response')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function trainerResponse(User $user, EntityManagerInterface $em, Request $request){
        /** @var \App\Entity\User $sender */
        $sender = $this->getUser();
        $this->logger->info('Trener/Admin {sender} odgovara korisniku {receiver}.', [
            'sender' => $sender->getUserIdentifier(),
            'receiver' => $user->getUserIdentifier()
        ]);

        $msg = new MessageRequest();
        $form = $this->createForm(MessageRequestType::class);

        if(!$form->isSubmitted()) {
            $form->get('email')->setData($user->getEmail());
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setDateSent(new DateTimeImmutable());
            $msg->setTimeSent(new DateTimeImmutable());
            $formData = $form->getData();
            $receiverMail = $formData['email'];
            $receiver = $em->getRepository(User::class)->findOneBy(['email' => $receiverMail]);

            if (!$receiver || !$receiver->getRole() === UserRole::USER) {
                $this->logger->warning('Odgovor trenera neuspešan. Ciljani korisnik {receiver} nije klijent.', ['receiver' => $receiverMail]);
                $this->addFlash('warning', 'Korisnik kome zelite poslati zahtev nije klijent');
                return $this->redirectToRoute('app_send_msg');
            }

            $msg->setSender($sender);
            $msg->setReceiver($receiver);
            $msg->setContent($formData['content']);
            $em->persist($msg);
            $em->flush();

            $this->logger->info('Trener/Admin {sender} je uspešno poslao odgovor korisniku {receiver}.', [
                'sender' => $sender->getUserIdentifier(),
                'receiver' => $receiver->getUserIdentifier()
            ]);

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('message/user_send_form.html.twig', [
            'messageForm' => $form,
        ]);
    }
    #[Route('/user-inbox', name:'app_usr_inbox')]
    #[IsGranted('ROLE_USER')]
    public function viewTrainerResponses(EntityManagerInterface $em)
    {
        /** @var \App\Entity\User $receiver */
        $receiver = $this->getUser();
        $this->logger->info('Korisnik {user} pregleda inbox za odgovore od trenera.', ['user' => $receiver->getUserIdentifier()]);

        $messages = $em
            ->getRepository(MessageRequest::class)
            ->findBy(
                ['receiver' => $receiver],
                ['date_sent' => 'DESC', 'time_sent' => 'DESC']
            );

        return $this->render('message/user_inbox.html.twig', [
            'messages' => $messages
        ]);
    }


}

?>