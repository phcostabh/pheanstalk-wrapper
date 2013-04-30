<?php namespace SS\Queue;

use Pheanstalk_Job;

interface QueueInterface {

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return void
     */
    public function push($job, $data = '', $queue = null);

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  int     $delay
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return void
     */
    public function later($delay, $job, $data = '', $queue = null);

    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Illuminate\Queue\Jobs\Job|nul
     */
    public function pop($queue = null);

    /**
     * Delete a job from the queue.
     *
     * @param  Pheanstalk_Job  $job
     * @return void
     */
    public function deleteJob(Pheanstalk_Job $job);

}
