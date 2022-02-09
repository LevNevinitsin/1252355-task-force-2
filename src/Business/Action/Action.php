<?php
namespace LevNevinitsin\Business\Action;

abstract class Action
{
    abstract public function getName(): string;
    abstract public function getInternalTitle(): string;
    abstract public function isUserAuthorized(int $userId, string $userRole, int $customerId, ?int $contractorId = null): bool;
}

