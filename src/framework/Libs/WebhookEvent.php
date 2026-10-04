<?php

namespace Boctulus\Simplerest\Core\Libs;

class WebhookEvent
{
    protected string $eventType;
    protected string $entity;
    protected array $data;
    protected $id;
    protected ?int $actorId;
    protected array $scopeContext;
    protected bool $showFieldsRequested;
    protected string $occurredAt;

    public function __construct(
        string $eventType,
        string $entity,
        array $data,
        $id = null,
        ?int $actorId = null,
        array $scopeContext = [],
        bool $showFieldsRequested = false,
        ?string $occurredAt = null
    ) {
        $this->eventType = $eventType;
        $this->entity = $entity;
        $this->data = $data;
        $this->id = $id;
        $this->actorId = $actorId;
        $this->scopeContext = $scopeContext;
        $this->showFieldsRequested = $showFieldsRequested;
        $this->occurredAt = $occurredAt ?? date('Y-m-d H:i:s');
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getActorId(): ?int
    {
        return $this->actorId;
    }

    public function getScopeContext(): array
    {
        return $this->scopeContext;
    }

    public function showFieldsRequested(): bool
    {
        return $this->showFieldsRequested;
    }

    public function getOccurredAt(): string
    {
        return $this->occurredAt;
    }
}
