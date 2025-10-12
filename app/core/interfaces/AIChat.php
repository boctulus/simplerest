<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface AIChat {
    function setParams(Array $arr);
    function addContent($content, $role = 'user');
    function getClient();
    function exec($model = null);
    function getTokenUsage();
    function isComplete();
    function getContent($decode = false);
    function getFinishReason();
}