<?php

use Mockery as m;

class PheanstalkQueueTest extends PHPUnit_Framework_TestCase {

    public function setUp(){
        $this->queue = new SS\Queue\PheanstalkQueue(m::mock('Pheanstalk_Pheanstalk'), 'default');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testPushProperlyPushesJobOntoBeanstalkd()
    {
        $pheanstalk = $this->queue->getPheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('test_tube')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->twice()->with(json_encode(array('job' => 'foo', 'data' => array('data'))));

        $this->queue->push('foo', array('data'), 'test_tube');
        $this->queue->push('foo', array('data'));
    }


    public function testDelayedPushProperlyPushesJobOntoBeanstalkd()
    {
        $pheanstalk = $this->queue->getPheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('test_tube')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->twice()->with(json_encode(array('job' => 'foo', 'data' => array('data'))), Pheanstalk_Pheanstalk::DEFAULT_PRIORITY, 5);

        $this->queue->later(5, 'foo', array('data'), 'test_tube');
        $this->queue->later(5, 'foo', array('data'));
    }


    public function testPopProperlyPopsJobOffOfBeanstalkd()
    {
        $pheanstalk = $this->queue->getPheanstalk();
        $pheanstalk->shouldReceive('watchOnly')->once()->with('default')->andReturn($pheanstalk);
        $job = m::mock('Pheanstalk_Job');
        $pheanstalk->shouldReceive('reserve')->once()->andReturn($job);

        $result = $this->queue->pop();

        $this->assertInstanceOf('Pheanstalk_Job', $result);
    }

    public function testDeleteJobProperlyDeletesJobFromBeanstalkQueue()
    {
        $job = m::mock('Pheanstalk_Job');
        $pheanstalk = $this->queue->getPheanstalk();
        $pheanstalk->shouldReceive('delete')->once()->with($job);

        $this->queue->deleteJob($job);
    }

}

