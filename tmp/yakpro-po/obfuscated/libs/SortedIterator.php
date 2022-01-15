<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:55              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; class SortedIterator extends \SplHeap { public function __construct(\Iterator $yIcPB) { foreach ($yIcPB as $k4w9W) { $this->insert($k4w9W); sVrn7: } FbyNC: } public function compare($vLwiT, $CebYR) { return strcmp($CebYR->getRealpath(), $vLwiT->getRealpath()); } }
