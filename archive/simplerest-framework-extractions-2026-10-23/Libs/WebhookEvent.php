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
    protected string $eventId;

    public function __construct(
        string $eventType,
        string $entity,
        array $data,
        $id = null,
        ?int $actorId = null,
        array $scopeContext = [],
        bool $showFieldsRequested = false,
        ?string $occurredAt = null,
        ?string $eventId = null
    ) {
        $this->eventType = $eventType;
        $this->entity = $entity;
        $this->data = $data;
        $this->id = $id;
        $this->actorId = $actorId;
        $this->scopeContext = $scopeContext;
        $this->showFieldsRequested = $showFieldsRequested;
        $this->occurredAt = $occurredAt ?? date('Y-m-d H:i:s');
        $this->eventId = $eventId === null
            ? $this->generateEventId()
            : $this->validateEventId($eventId);
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

    public function getEventId(): string
    {
        return $this->eventId;
    }

    private function generateEventId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function validateEventId(string $eventId): string
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $eventId)) {
            throw new \InvalidArgumentException('Webhook event ID must be a UUID v4.');
        }

        return strtolower($eventId);
    }
}
