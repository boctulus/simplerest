<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\IProcessable;

/**
 * Parallex Task Manager
 * 
 * This class manages parallel tasks with locking mechanisms to prevent concurrent execution.
 * 
 * @author Pablo Bozzolo boctulus@gmail.com
 */
class Parallex
{
    /**
     * @var int|null The current offset for processing tasks.
     */
    protected $offset;

    /**
     * @var int The minimum time in seconds for locking tasks.
     */
    protected $min_secs_t_locked = 120;

    /**
     * @var int The maximum time in seconds for locking tasks.
     */
    protected $max_secs_t_locked = 300;

    /**
     * @var string The name of the transient used for storing task state.
     */
    protected $transient_name = 'parallex';

    /**
     * @var IProcessable The handler for the processable tasks.
     */
    protected $processHandler;

    /**
     * Constructor method for Parallex.
     *
     * @param IProcessable $processHandler The handler for the processable tasks.
     * @param int|null $min_t_locked The minimum time in seconds for locking tasks. Default is null.
     * @param int|null $max_t_locked The maximum time in seconds for locking tasks. Default is null.
     */
    public function __construct(IProcessable $processHandler, $min_t_locked = null, $max_t_locked = null)
    { 
        $this->processHandler = $processHandler;

        if ($min_t_locked !== null){
            $this->min_secs_t_locked = $min_t_locked;
        }

        if ($max_t_locked !== null){
            $this->max_secs_t_locked = $max_t_locked;
        }

        // Check if the maximum locking time is exceeded and unlock if necessary
        $this->checkMaxTimeLocked();
    }

    /**
     * Check if the maximum locking time is exceeded and unlock if necessary.
     */
    protected function checkMaxTimeLocked(){
        $state = $this->getState();

        if ($state !== false && $state['lock']) {
            $start_time = $state['locked_time'];

            if ($start_time !== null) {
                
                $current_time = time();
                $elapsed_time = $current_time - $start_time;

                if ($elapsed_time > $this->max_secs_t_locked) {
                    // Unlock if the maximum locking time is exceeded
                    $this->setLock(false);
                }
            }
        }
    }

    /**
     * Set the state of the task.
     *
     * @param array $data The data to set as the task state.
     */
    protected function setState($data)
    {
        set_transient($this->transient_name, $data);
    }

    /**
     * Initialize the state of the task.
     *
     * @param int|null $offset The offset for processing tasks. Default is null.
     * @param bool|null $lock Whether to lock the task. Default is null.
     */
    protected function initState($offset = null, $lock = null)
    {
        if ($offset === null) {
            $offset = 0;
        }

        if ($lock === null) {
            $lock = false;
        }

        $data = [
            'rows'        => $this->processHandler::count(),
            'offset'      => $offset,
            'lock'        => $lock,
            'locked_time' => $lock ? time() : null,
        ];

        // Initialize the task state
        $this->setState($data);
    }

    /**
     * Get the state of the task.
     *
     * @return mixed The state of the task, or false if not found.
     */
    public function getState()
    {
        return get_transient($this->transient_name);
    }

    /**
     * Clear the task state.
     */
    public function clear()
    {
        delete_transient($this->transient_name);
    }

    /**
     * Check if the task is locked.
     *
     * @return bool True if the task is locked, false otherwise.
     */
    public function isLocked()
    {
        $state = $this->getState();

        if ($state === false) {
            return false;
        }

        return $state['lock'];
    }

    /**
     * Check if the task is time locked.
     *
     * @return bool True if the task is time locked, false otherwise.
     */
    public function isTimeLocked()
    {
        $state = $this->getState();

        if ($state === false) {
            return false;
        }

        $start_time = $state['locked_time'];

        if ($start_time === null) {
            return false;
        }

        $current_time = time();
        $elapsed_time = $current_time - $start_time;

        return ($elapsed_time <= $this->min_secs_t_locked);
    }
    
    // lock / unlock

    /**
     * Set the lock status of the task.
     *
     * @param bool $val The value to set for the lock status.
     * @return bool True if the lock status was set successfully, false otherwise.
     */
    public function setLock(bool $val)
    {
        $state = $this->getState();

        if ($this->isTimeLocked()){
            return false;
        }

        if ($val === true) {
            $state['locked_time'] = time();
        } else {
            $state['locked_time'] = null;
        }

        $state['lock'] = $val;

        set_transient($this->transient_name, $state);

        return true;
    }

    /**
     * Set the offset for processing tasks.
     *
     * @param int $val The value to set for the offset.
     * @return bool True if the offset was set successfully, false otherwise.
     */
    protected function setOffset(int $val)
    {
        $state = $this->getState();

        if ($state === false) {
            $this->initState($val);
            return false;
        }

        $state['offset'] = $val;

        if ($val === 0){
            $state['lock'] = false;
        }

        set_transient($this->transient_name, $state);

        return true;
    }

    /**
     * Get the current offset for processing tasks.
     *
     * @return int|null The current offset for processing tasks, or null if not found.
     */
    public function getOffset()
    {
        $state = $this->getState();

        if ($state === false) {
            return false;
        }

        return $state['offset'] ?? null;
    }

    public function reset(){
        return $this->setOffset(0);
    }

    /**
     * Check if all tasks have been processed.
     *
     * @param int $rows The total number of rows to process.
     * @param int $offset The current offset for processing tasks.
     * @return bool True if all tasks have been processed, false otherwise.
     */
    protected function isDone($rows, $offset)
    {
        $res = ($offset >= $rows - 1);

        if ($res) {
            dd("Done. ALL lots were already processed");
        }

        return $res;
   
    }

    /**
     * Run the task.
     *
     * @param int $limit The row limit for processing tasks
     * @return void
     */
    public function run(int $limit)
    {
        if ($this->getState() === false){
            $rows = $this->processHandler::count();
    
            // Lock before starting
            $this->initState(0, true);
    
            $this->processHandler::run(null, 0, $limit);
    
            // Check for the first page
            if ($rows > $limit){
                $offset = $limit;
            }
            
            if ($this->isDone($rows, $offset)){
                // Lock completely
                $this->setOffset(-1);            
            } else {
                $this->setOffset($offset);  
            }
    
            $this->setLock(false);      
        } else {
            $data = $this->getState();
    
            dd($data, 'T');
    
            // If there is data in the State, continue from where it left off
            $rows        = $data['rows'];
            $offset      = $data['offset'];
            $lock        = $data['lock'];
    
            // Check if all records have been processed
            if ($offset >= $rows) {
                // Completely lock because all batches processing is completed        
                $offset = -1;
            } else {
                // Check if the process is locked
                if (!$lock) {
                    // Lock before starting
                    $this->setLock(true);
    
                    // Process batch
                    $this->processHandler::run(null, $offset, $limit);
    
                    // Calculate the new offset for the next iteration
                    $offset = $offset + $limit;
    
                    if ($this->isDone($rows, $offset)){
                        // Completely lock
                        $this->setOffset(-1);            
                    } else {
                        $this->setOffset($offset);  
                    }
    
                    $this->setLock(false);                
                    
                    dd($data, 'T');
                } else {
                    dd("LOCKED");
                }
            }
        }
    }
}
