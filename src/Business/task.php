<?php
namespace LevNevinitsin\Business;

use LevNevinitsin\Business\Action\Action;
use LevNevinitsin\Business\Action\CancelAction;
use LevNevinitsin\Business\Action\RespondAction;
use LevNevinitsin\Business\Action\StartAction;
use LevNevinitsin\Business\Action\DeclineAction;
use LevNevinitsin\Business\Action\CompleteAction;
use LevNevinitsin\Business\Exception\TaskException;

class Task
{
    const STATUS_NEW       = 'new';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_AT_WORK   = 'at work';
    const STATUS_DONE      = 'done';
    const STATUS_FAILED    = 'failed';

    private $namesMap = [
        self::STATUS_NEW       => 'Новое',
        self::STATUS_CANCELLED => 'Отменено',
        self::STATUS_AT_WORK   => 'В работе',
        self::STATUS_DONE      => 'Выполнено',
        self::STATUS_FAILED    => 'Провалено',
    ];

    private $customerId;
    private $contractorId;
    private $currentStatus;

    private $actionCancel;
    private $actionRespond;
    private $actionStart;
    private $actionDecline;
    private $actionComplete;

    private $actionsMap;
    private $transitionsMap;

    public function __construct(string $taskStatus, int $customerId, ?int $contractorId = null)
    {
        $this->customerId = $customerId;
        $this->contractorId = $contractorId;

        $reflection = new \ReflectionClass(__CLASS__);
        $constants = $reflection->getConstants();
        $statusConstants = array_filter($constants, function($constantName): bool {
            return str_starts_with($constantName, 'STATUS');
        }, ARRAY_FILTER_USE_KEY);

        $taskStatusConstName = array_search($taskStatus, $statusConstants, true);

        if (!$taskStatusConstName) {
            throw new TaskException("статуса \"{$taskStatus}\" не существует.");
        }

        $this->currentStatus = $statusConstants[$taskStatusConstName];

        $this->actionCancel   = new CancelAction();
        $this->actionRespond  = new RespondAction();
        $this->actionStart    = new StartAction();
        $this->actionDecline  = new DeclineAction();
        $this->actionComplete = new CompleteAction();

        $this->actionsMap = [
            self::STATUS_NEW => [
                $this->actionCancel,
                $this->actionRespond,
                $this->actionStart,
            ],
            self::STATUS_AT_WORK => [
                $this->actionDecline,
                $this->actionComplete,
            ],
        ];

        $this->transitionsMap = [
            $this->actionCancel->getName()   => self::STATUS_CANCELLED,
            $this->actionRespond->getName()  => self::STATUS_NEW,
            $this->actionStart->getName()    => self::STATUS_AT_WORK,
            $this->actionDecline->getName()  => self::STATUS_FAILED,
            $this->actionComplete->getName() => self::STATUS_DONE,
        ];

    }

    public function getNextStatus(Action $action): ?string
    {
        return $this->transitionsMap[$action->getName()] ?? null;
    }

    public function getAvailableActions(int $userId, string $userRole): array
    {
        $possibleRoles = ['customer', 'contractor'];

        if (!in_array($userRole, $possibleRoles)) {
            throw new TaskException("роли \"{$userRole}\" не существует.");
        }

        $allActions = $this->actionsMap[$this->currentStatus] ?? [];

        $customerId = $this->customerId;
        $contractorId = $this->contractorId;

        return array_filter($allActions, function ($action) use ($userId, $userRole, $customerId, $contractorId): bool {
            return $action->isUserAuthorized($userId, $userRole, $customerId, $contractorId);
        });
    }

    public function takeAction(Action $action): void
    {
        $this->currentStatus = $this->transitionsMap[$action->getName()];
    }
}
