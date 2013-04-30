<?php

use Pheanstalk_Pheanstalk as Pheanstalk;

class PheanstalkProofOfConcept extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->pheanstalk = new Pheanstalk('127.0.0.1');
    }

    public function testShoudBeAnInstanceOfPheansTalk()
    {
        $this->assertInstanceOf('Pheanstalk_Pheanstalk', $this->pheanstalk);
    }

    public function testShouldBeListening()
    {
        $this->assertTrue($this->pheanstalk->getConnection()->isServiceListening());
    }

    public function testCreateNewQueueJobs()
    {
        $this->pheanstalk->useTube('test_tube')->put('Job', json_encode(array('bla' => 'bla')));
        $this->assertContains('test_tube', $this->pheanstalk->listTubes());
    }

    public function testJobExecution()
    {
        $job = $this->pheanstalk->watchOnly('test_tube')->reserve();
        $this->assertInstanceOf('Pheanstalk_Job', $job);
        $this->assertEquals('Job', $job->getData());
    }

}
