<?php

namespace Mykhailok\SupportChat\Cron;

class StatusObserverCronjob
{
    private \Mykhailok\SupportChat\Model\ChatPriorityObserver $chatPriorityObserver;

    /**
     * StatusObserverCronjob constructor.
     * @param \Mykhailok\SupportChat\Model\ChatPriorityObserver $chatPriorityObserver
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ChatPriorityObserver $chatPriorityObserver
    ) {
        $this->chatPriorityObserver = $chatPriorityObserver;
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $this->chatPriorityObserver->applyWaitingPriority();
    }
}
