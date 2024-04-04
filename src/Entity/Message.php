<?php

namespace App\Entity;

use App\Entity\MessageStatusEnum;
use App\Repository\MessageRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
/**
 * TODO: Review Message class
 */

class Message
{
    public function __construct(string $text, ?MessageStatusEnum $status = null)
    {
        $this->uuid = Uuid::v6()->toRfc4122();
        $this->text = $text;
        $this->createdAt = new DateTimeImmutable();
        $this->status = $status ?? MessageStatusEnum::PENDING; // Default to PENDING if no status provided
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
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

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

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

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
}
