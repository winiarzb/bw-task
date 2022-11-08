<?php


namespace App\Service;

use App\Entity\Meeting;
use App\Interface\EntityInterface;
use DateTimeImmutable;

class MeetingStatusResolver
{
    private const STATUS_OPEN_TO_REGISTRATION = 'open to registration';
    private const STATUS_FULL = 'full';
    private const STATUS_IN_SESSION = 'in session';
    private const STATUS_DONE = 'done';

    private function getMeetingStatus(Meeting $entity): string
    {
        $status = '';

        $status = $this->isOpenToRegistration($entity) ? self::STATUS_OPEN_TO_REGISTRATION : $status;
        $status = $this->isFull($entity) ? self::STATUS_FULL : $status;
        $status = $this->isInSession($entity) ? self::STATUS_IN_SESSION : $status;
        $status = $this->isDone($entity) ? self::STATUS_DONE : $status;

        return $status;
    }

    private function isOpenToRegistration(Meeting $meeting): bool
    {
        $meetingIsFull = $meeting->participants->count() >= $meeting->participantsLimit;
        $dateTimeNow = new DateTimeImmutable('now');

        if ($meeting->startTime > $dateTimeNow && $meeting->endTime > $dateTimeNow && !$meetingIsFull) {
            return true;
        }

        return false;
    }

    private function isFull(Meeting $meeting): bool
    {
        $meetingIsFull = $meeting->participants->count() >= $meeting->participantsLimit;
        $dateTimeNow = new DateTimeImmutable('now');

        if ($meeting->startTime <= $dateTimeNow && $meetingIsFull) {
            return true;
        }

        return false;
    }

    private function isInSession(Meeting $meeting): bool
    {
        $dateTimeNow = new DateTimeImmutable('now');

        if ($meeting->startTime <= $dateTimeNow && $meeting->endTime > $dateTimeNow) {
            return true;
        }

        return false;
    }

    private function isDone(Meeting $meeting): bool
    {
        $dateTimeNow = new DateTimeImmutable('now');

        if ($meeting->endTime <= $dateTimeNow) {
            return true;
        }

        return false;
    }
}
