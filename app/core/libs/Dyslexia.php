<?php

namespace simplerest\core\libs;

/*
    @author Pablo Bozzolo
*/

class Dyslexia
{
    // pre-condicion: no enviar una cadena vacia... o devuelve un array (vacio)

    private $word;
    private $n;   // long

    const startchar = 1;

    public function __construct($wd)
    {
        $this->word = $wd;
        $this->n   = strlen($this->word);
    }

    function inversion()
    {
        $i = rand(static::startchar, $this->n - 1);
        $out = $this->word;

        $temp = $out[$i];
        $out[$i] = $out[$i + 1];
        $out[$i + 1] = $temp;

        return $out;
    }

    function supresion()
    {
        $i = rand(static::startchar, $this->n - 1);
        return Strings::middle($this->word, 0, $i - 1) . Strings::middle($this->word, $i + 1);
    }

    function repeticion()
    {
        $i = rand(static::startchar, $this->n - 1);
        return Strings::middle($this->word, 0, $i) . Strings::middle($this->word, $i, strlen($this->word));
    }

    function reemplazo()
    {
        $i = rand(static::startchar, $this->n - 1);
        return Strings::middle($this->word, 0, $i - 1) . dame_key_vecina($this->word[$i]) . Strings::middle($this->word, $i + 1);
    }

    function insercion()
    {
        $i = rand(static::startchar, $this->n - 1);
        return Strings::middle($this->word, 0, $i) . dame_key_vecina($this->word[$i]) . Strings::middle($this->word, $i + 1);
    }

    function getAnyChange()
    {
        // devuelve una variacion de cualquier tipo
        $n = rand(1, 5);

        switch ($n) {
            case 1:
                return $this->inversion();
                break;
            case 2:
                return $this->supresion();
                break;
            case 3:
                return $this->repeticion();
                break;
            case 4:
                return $this->reemplazo();
                break;
            case 5:
                return $this->insercion();
                break;
        }
    }

    function getWord()
    {
        return $this->word;
    }

    function __tostring()
    {
        return ($this->getWord());
    }
} # fin de la clase

function dame_una($str)
{
    $n = strlen($str);
    return $str[rand(0, $n - 1)];
}


function dame_key_vecina($key)
{
    // en teclado en español

    $key_original =  $key;
    $key = strtolower($key);

    $vecina['q'] = 'qwsz';
    $vecina['w'] = 'qasde';
    $vecina['e'] = 'wrsdf';
    $vecina['t'] = 'ryfgh';
    $vecina['y'] = 'tghju';
    $vecina['u'] = 'yhjki';
    $vecina['i'] = 'ujklo';
    $vecina['o'] = 'iklp';
    $vecina['p'] = 'ol';

    $vecina['a'] = 'qwsxz';
    $vecina['s'] = 'aqwedxz';
    $vecina['d'] = 'swerfcx';
    $vecina['f'] = 'dertgvc';
    $vecina['g'] = 'ftyhbv';
    $vecina['h'] = 'gyujnb';
    $vecina['j'] = 'huikmn';
    $vecina['k'] = 'jiolm';
    $vecina['l'] = 'kop';

    $vecina['z'] = 'asx';
    $vecina['x'] = 'zsdc';
    $vecina['c'] = 'xdfv';
    $vecina['v'] = 'cfgb';
    $vecina['b'] = 'vghn';
    $vecina['n'] = 'bhjm';
    $vecina['m'] = 'njk';

    if (isset($vecina[$key])) {
        $salida = (dame_una($vecina[$key]));
        if ($key != $key_original) {
            $salida = chr(ord($salida) - 32);  // lo paso a mayusculas de nuevo
        }
        return $salida;
    } else {
        return $key_original;
    }
}

