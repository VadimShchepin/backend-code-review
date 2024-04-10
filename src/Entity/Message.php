<?php

namespace App\Entity;

use App\Entity\MessageStatusEnum;
use App\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]

class Message
{
    //Constructor can be used to generate values to move the irrelevant logic from Handler
    public function __construct(string $text, ?MessageStatusEnum $status = null)
    {
        $this->uuid = Uuid::v7()->toRfc4122();
        /*UUIDv7 can be used -> https://symfony.com/doc/current/components/uid.html
        provides better entropy and a more strict chronological order of UUID generation*/

        $this->text = $text;
        $this->createdAt = new DateTimeImmutable();
        $this->status = $status ?? MessageStatusEnum::SENT;
        // Default to SENT if no status provided
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID,)]
    private ?string $uuid;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column(type: 'string', nullable: false, enumType: MessageStatusEnum::class)]
    private MessageStatusEnum $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    // Set UUID shouldn't be possible if set in constructor
//    public function setUuid(string $uuid): self
//    {
//        $this->uuid = $uuid;
//
//        return $this;
//    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): MessageStatusEnum
    {
        return $this->status;
    }

    public function setStatus(MessageStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable

    {
        return $this->createdAt;
    }
    // Shouldn't be set after initialisation in constructor
//    public function setCreatedAt(DateTimeImmutable $createdAt): self
//    {
//        $this->createdAt = $createdAt;
//
//        return $this;
//    }
}
