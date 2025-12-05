<?php

namespace Boctulus\Simplerest\core\traits;

use \Boctulus\Simplerest\Core\Libs\InMemoryCache;
use \Boctulus\Simplerest\Core\Libs\DBCache;
use Boctulus\Simplerest\Core\Interfaces\ICache;
use Boctulus\Simplerest\Core\Interfaces\IObserver;
use Boctulus\Simplerest\Core\Interfaces\ISubject;

/**
 * Trait EventBusTrait
 *
 * This trait it's an Event Dispatcher which implements ISubject interface methods for an event bus.
 * It provides an optional mechanism to persist the last event using a configurable cache.
 *
 * Author: Pablo Bozzolo
 */
trait EventBusTrait {
    /**
     * Registered observers.
     *
     * @var array
     */
    protected array $observers = [];

    /**
     * Class name of the cache mechanism to use (must implement ICache).
     *
     *
     * @var string
     */
    protected static string $cacheMechanism = DBCache::class;

    /**
     * Attach an observer.
     *
     * @param IObserver $observer
     * @return void
     */
    public function attach(IObserver $observer): void {
        $this->observers[spl_object_hash($observer)] = $observer;
    }

    /**
     * Detach an observer.
     *
     * @param IObserver $observer
     * @return void
     */
    public function detach(IObserver $observer): void {
        $key = spl_object_hash($observer);
        if (isset($this->observers[$key])) {
            unset($this->observers[$key]);
        }
    }

    /**
     * Notify all observers with the provided data.
     *
     * In addition to notifying the observers, the event is persisted using the selected cache mechanism.
     *
     * @param mixed $data Data to send to observers.
     * @return void
     */
    public function notify(mixed $data = null): void {
        // Notify each observer.
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }

        // Persist the last event using the configured cache mechanism.
        $cacheClass = static::$cacheMechanism;
        if (class_exists($cacheClass) && is_subclass_of($cacheClass, ICache::class)) {
            $eventKey = 'eventbus_last_event';
            $cacheClass::put($eventKey, $data, 604800);
        }
    }

    /**
     * Set the cache mechanism for event persistence.
     *
     * The parameter should be a fully qualified class name that implements ICache.
     *
     * @param string $cacheClass
     * @return void
     * @throws \InvalidArgumentException
     */
    public static function setCacheMechanism(string $cacheClass): void {
        if (!is_subclass_of($cacheClass, ICache::class)) {
            throw new \InvalidArgumentException("Provided class must implement ICache interface.");
        }
        static::$cacheMechanism = $cacheClass;
    }

    /**
     * Retrieve the last persisted event from the cache.
     *
     * @return mixed|null
     */
    public static function getLastEvent(): mixed {
        $cacheClass = static::$cacheMechanism;
        if (class_exists($cacheClass) && is_subclass_of($cacheClass, ICache::class)) {
            $eventKey = 'eventbus_last_event';
            return $cacheClass::get($eventKey);
        }
        return null;
    }
}
