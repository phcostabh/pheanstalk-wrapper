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
        $jobData = json_encode(array(
            'job' => 'foot',
            'data'=> array(
                'id'   => 1,
                'name' => 'foo'
            )
        ));

        $job = $this->_createJob('test_tube', $jobData);
        $this->assertInstanceOf('Pheanstalk_Job', $job);
        $this->assertEquals($jobData, $job->getData());
        $this->_deleteJob('test_tube', $job);
    }

    public function testPeedTheNextReadyJob(){
        $job = $this->_createJob('test_tube', 'Job');
        $readyJobs = $this->pheanstalk->useTube('test_tube')->peekReady();
        $this->assertNotNull($readyJobs);
        $this->_deleteJob('test_tube',$job);
    }

    public function testPeedTheNextDelayedJob(){
        $job = $this->_createJob('test_tube', 'Job', 20);
        $readyJobs = $this->pheanstalk->useTube('test_tube')->peekDelayed();
        $this->assertNotNull($readyJobs);
        $this->_deleteJob('test_tube',$job);
    }

    public function testPeedTheNextBuriedJob(){
        $this->_createJob('test_tube', 'Job');
        $job = $this->pheanstalk->watchOnly('test_tube')->reserve();
        $this->pheanstalk->useTube('test_tube')->bury($job);
        $readyJobs = $this->pheanstalk->useTube('test_tube')->peekBuried();
        $this->assertNotNull($readyJobs);
        $this->_deleteJob('test_tube',$job);
    }

    /**
     * @return Pheanstalk_Job
     */
    public function _createJob($queue, $job, $delay = 0, $priority = Pheanstalk_Pheanstalk::DEFAULT_PRIORITY){
        $jobID = $this->pheanstalk->useTube($queue)->put($job, $priority, $delay);
        return $this->_peekJob($queue, $jobID);
    }

    /**
     * @return integer $id
     */
    public function _deleteJob($queue, $job){
        return $this->pheanstalk->useTube($queue)->delete($job);
    }

    /**
     * @return Pheanstalk_Job
     */
    public function _peekJob($queue, $job){
        return $this->pheanstalk->useTube($queue)->peek($job);
    }

}
