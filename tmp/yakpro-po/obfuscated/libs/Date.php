<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:55              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; class Date { function nextWorkingDay(string $pegMI, string $pVFNz = "\x59\55\155\55\x64") { goto ukXP8; w0vQ3: return $pegMI->format($pVFNz); goto i8iE_; ukXP8: $pegMI = new \DateTime($pegMI); goto Pmpcj; Pmpcj: $pegMI->modify("\x2b\61\40\167\x65\x65\x6b\x64\x61\171"); goto w0vQ3; i8iE_: } }
