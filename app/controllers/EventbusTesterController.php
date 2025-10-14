<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\EventBus;
use Boctulus\Simplerest\Core\Libs\InMemoryCache;
use Boctulus\Simplerest\Core\Libs\FileCache;
use Boctulus\Simplerest\Core\Libs\DBCache;
use Boctulus\Simplerest\Core\libs\Strings;
use Boctulus\Simplerest\Libs\SampleObserver;

class EventbusTesterController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        // -----------------------------
        // Testing with InMemoryCache
        // -----------------------------

        // Set the cache mechanism to InMemoryCache
        EventBus::setCacheMechanism(InMemoryCache::class);

        $eventBus = new EventBus();

        // Create and attach an observer
        $observer = new SampleObserver();
        $eventBus->attach($observer);

        // Dispatch an event; this should notify the observer and persist the event.
        $eventBus->notify("Random data: " . Strings::randomHexaString(6));

        /*
            Retrieve and print the last event from cache.
        */

        $lastEvent = EventBus::getLastEvent();
        
        dd($lastEvent, "Last event from Cache");

        // -----------------------------
        // Optional: Testing with another Cache (e.g., FileCache or DBCache)
        // -----------------------------
        // Uncomment the following block if FileCache or DBCache is properly configured.
        // EventBus::setCacheMechanism(FileCache::class);
        // $eventBus->notify("Test event data with FileCache");
        // $lastEvent = EventBus::getLastEvent();
        // echo "Last event from FileCache: " . var_export($lastEvent, true) . PHP_EOL;            
    }

    function test_multiple_observers()
    {            
        /*
            Set the cache mechanism to InMemoryCache
        */
        // EventBus::setCacheMechanism(FileCache::class);
        EventBus::setCacheMechanism(DBCache::class);

        // Initialize the EventBus
        $eventBus = new EventBus();

        // Create and attach multiple observers
        $observer1 = new SampleObserver();
        $observer2 = new SampleObserver();
        $observer3 = new SampleObserver();

        $eventBus->attach($observer1);
        $eventBus->attach($observer2);
        $eventBus->attach($observer3);

        // Dispatch an event; all observers should be notified.
        $eventBus->notify("Random data #1: " . Strings::randomHexaString(6) . " sent at ". at());
        $eventBus->notify("Random data #2: " . Strings::randomHexaString(6) . " sent at ". at());        
        $eventBus->notify("Random data #3: " . Strings::randomHexaString(6) . " sent at ". at());        

        // Retrieve and print the last event from cache.
        $lastEvent = EventBus::getLastEvent();

        dd($lastEvent, "Last event from Cache");
    }

}

