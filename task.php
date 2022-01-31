<?php

class Task
{
    const STATUS_NEW       = 'new';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_AT_WORK   = 'at work';
    const STATUS_DONE      = 'done';
    const STATUS_FAILED    = 'failed';

    const ACTION_CANCEL  = 'cancel';
    const ACTION_RESPOND = 'respond';
    const ACTION_CONFIRM = 'confirm';
    const ACTION_DECLINE = 'decline';

    private $namesMap = [
        self::STATUS_NEW       => 'Новое',
        self::STATUS_CANCELLED => 'Отменено',
        self::STATUS_AT_WORK   => 'В работе',
        self::STATUS_DONE      => 'Выполнено',
        self::STATUS_FAILED    => 'Провалено',
        self::ACTION_CANCEL    => 'Отменить',
        self::ACTION_RESPOND   => 'Откликнуться',
        self::ACTION_CONFIRM   => 'Выполнено',
        self::ACTION_DECLINE   => 'Отказаться',
    ];

    private $transitionsMap = [
        self::STATUS_NEW => [
            self::ACTION_CANCEL => self::STATUS_CANCELLED,
            self::ACTION_RESPOND => self::STATUS_AT_WORK,
        ],
        self::STATUS_AT_WORK => [
            self::ACTION_CONFIRM => self::STATUS_DONE,
            self::ACTION_DECLINE => self::STATUS_FAILED,
        ],
    ];

    private $executorId;
    private $clientId;
    private $currentStatus;

    public function __construct(int $executorId, int $clientId)
    {
        $this->executorId = $executorId;
        $this->clientId = $clientId;
        $this->currentStatus = self::STATUS_NEW;
    }

    public function getNextStatus(string $action): ?string
    {
        return $this->transitionsMap[$this->currentStatus][$action] ?? null;
    }

    public function getAvailableActions(): ?string
    {
        $actions = array_keys($this->transitionsMap[$this->currentStatus] ?? []);
        return implode(', ', $actions);
    }

    public function takeAction(string $action)
    {
        $this->currentStatus = $this->transitionsMap[$this->currentStatus][$action];
    }
}
