<?php
namespace LevNevinitsin\Business\Action;

abstract class Action
{
    abstract public function getName(): string;
    abstract public function getInternalTitle(): string;
    abstract public function isUserAuthorized(int $customerId, int $contractorId, int $userId): bool;
}

