<?php
namespace LevNevinitsin\Business;

use app\models\Task as ModelsTask;
use LevNevinitsin\Business\Action\Action;
use LevNevinitsin\Business\Action\CancelAction;
use LevNevinitsin\Business\Action\AcceptAction;
use LevNevinitsin\Business\Action\DeclineAction;
use LevNevinitsin\Business\Action\CompleteAction;

class Task
{
    const STATUS_CANCELLED = 2;
    const STATUS_AT_WORK   = 3;
    const STATUS_FAILED    = 4;
    const STATUS_DONE      = 5;

    private $actionCancel;
    private $actionAccept;
    private $actionDecline;
    private $actionComplete;
    private $task;

    public function __construct(ModelsTask $task)
    {
        $this->task = $task;

        $this->actionCancel   = new CancelAction();
        $this->actionAccept   = new AcceptAction();
        $this->actionDecline  = new DeclineAction();
        $this->actionComplete = new CompleteAction();

        $this->transitionsMap = [
            $this->actionCancel->getName()   => self::STATUS_CANCELLED,
            $this->actionAccept->getName()   => self::STATUS_AT_WORK,
            $this->actionDecline->getName()  => self::STATUS_FAILED,
            $this->actionComplete->getName() => self::STATUS_DONE,
        ];

    }

    /**
     * Performs an action on a task: changes task status id, updates "date_updated" field and stores information
     * into database.
     *
     * @param Action $action
     * @return void
     */
    public function takeAction(Action $action): void
    {
        $this->task->task_status_id = $this->transitionsMap[$action->getName()];
        $this->task->date_updated = date("Y-m-d H:i:s");
        $this->task->save();
    }
}
