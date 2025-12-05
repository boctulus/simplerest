<?php

namespace Boctulus\Simplerest\libs;

use Boctulus\Simplerest\Core\Interfaces\IObserver;

/**
 * Class SampleObserver
 *
 * A simple observer that prints the received event data.
 *
 * Author: Pablo Bozzolo
 */
class SampleObserver implements IObserver {
    /**
     * Update method called when the subject notifies observers.
     *
     * @param mixed $data
     * @return void
     */
    public function update(mixed $data): void {
        dd($data, "Observer received data");
    }
}

