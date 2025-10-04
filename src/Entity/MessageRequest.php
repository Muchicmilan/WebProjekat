<?php

namespace App\Entity;

use App\Repository\MessageRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRequestRepository::class)]
class MessageRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private ?User $receiver = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_sent = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $time_sent = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSent(): ?\DateTimeImmutable
    {
        return $this->date_sent;
    }

    public function setDateSent(\DateTimeImmutable $date_sent): static
    {
        $this->date_sent = $date_sent;

        return $this;
    }

    public function getTimeSent(): ?\DateTimeImmutable
    {
        return $this->time_sent;
    }

    public function setTimeSent(\DateTimeImmutable $time_sent): static
    {
        $this->time_sent = $time_sent;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

        public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(User $user): static
    {
        $this->sender = $user;

        return $this;
    }

        public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(User $user): static
    {
        $this->receiver = $user;

        return $this;
    }
}
