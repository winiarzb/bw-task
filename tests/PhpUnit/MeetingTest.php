<?php

namespace App\Tests\PhpUnit;

use App\Entity\Meeting;
use App\Entity\User;
use App\Exception\ParticipantsLimitExceededException;
use App\Service\MeetingStatusSResolver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MeetingTest extends KernelTestCase
{
    public function testParticipantsLimit()
    {
        $meeting = new Meeting('Test meeting', new \DateTimeImmutable('now'));
        for($i = 0; $i < 5; $i++){
            $user = new User('Test name');
            $meeting->addAParticipant($user);
        }

        $this->assertSame(
            true,
            $meeting->isMeetingFull(),
            "actual value is not same as expected value"
        );
    }

    public function testParticipantsLimitExceed()
    {
        $this->expectException(ParticipantsLimitExceededException::class);

        $meeting = new Meeting('Test meeting', new \DateTimeImmutable('now'));
        for($i = 0; $i < 10; $i++){
            $user = new User('Test name');
            $meeting->addAParticipant($user);
        }
    }

    public function testMeetingStatus()
    {
        self::bootKernel();
        $meetingStatusResolver = $this->getContainer()->get('App\Services\MeetingStatusResolver');

        $meeting = new Meeting('Some Meeting', new \DateTimeImmutable('now - 30 minutes'));
        $this->assertSame('in session', $meetingStatusResolver->getStatus($meeting));

        $meeting = new Meeting('Some Meeting', new \DateTimeImmutable('now + 30 minutes'));
        $this->assertSame('open to registration', $meetingStatusResolver->getStatus($meeting));

        for($i = 0; $i < 5; $i++){
            $user = new User('Test name');
            $meeting->addAParticipant($user);
        }
        $this->assertSame('full', $meetingStatusResolver->getStatus($meeting));

        $meeting = new Meeting('Test Meeting', new \DateTimeImmutable('now +3 hours'));
        $this->assertSame('done', $meetingStatusResolver->getStatus($meeting));
    }
}
