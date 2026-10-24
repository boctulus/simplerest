<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

/*
    English pluralization without external dependencies.

    Port of the English plural ruleset of doctrine/inflector 2.1.0
    (Copyright (c) 2006-2015 Doctrine Project, MIT License), which the core
    used until 2026-10 for SubResourceHandler pivot aliases. Rules, their order
    and the evaluation algorithm are kept as-is so the output is identical:

    1. A word matching an uninflected pattern is returned unchanged.
    2. An irregular word (exact, case-insensitive) is substituted; the first
       letter keeps its case.
    3. Otherwise the first matching regular rule is applied.

    unit-tests/microcore/InflectorParityTest.php checks the output against a
    fixture generated with doctrine/inflector 2.1.0.
*/

class Inflector
{
    private const PLURAL = [
        ['(s)tatus$', '\1\2tatuses'],
        ['(quiz)$', '\1zes'],
        ['^(ox)$', '\1\2en'],
        ['([m|l])ouse$', '\1ice'],
        ['(matr|vert|ind)(ix|ex)$', '\1ices'],
        ['(x|ch|ss|sh)$', '\1es'],
        ['([^aeiouy]|qu)y$', '\1ies'],
        ['(hive|gulf)$', '\1s'],
        ['(?:([^f])fe|([lr])f)$', '\1\2ves'],
        ['sis$', 'ses'],
        ['([ti])um$', '\1a'],
        ['(tax)on$', '\1a'],
        ['(c)riterion$', '\1riteria'],
        ['(p)erson$', '\1eople'],
        ['(m)an$', '\1en'],
        ['(c)hild$', '\1hildren'],
        ['(f)oot$', '\1eet'],
        ['(buffal|her|potat|tomat|volcan)o$', '\1\2oes'],
        ['(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$', '\1i'],
        ['us$', 'uses'],
        ['(alias)$', '\1es'],
        ['(analys|ax|cris|test|thes)is$', '\1es'],
        ['s$', 's'],
        ['^$', ''],
        ['$', 's'],
    ];

    private const PLURAL_IRREGULAR = [
        'abuse' => 'abuses',
        'alga' => 'algae',
        'atlas' => 'atlases',
        'avalanche' => 'avalanches',
        'axis' => 'axes',
        'axe' => 'axes',
        'beef' => 'beefs',
        'blouse' => 'blouses',
        'brother' => 'brothers',
        'brownie' => 'brownies',
        'cache' => 'caches',
        'cafe' => 'cafes',
        'canvas' => 'canvases',
        'cave' => 'caves',
        'chateau' => 'chateaux',
        'child' => 'children',
        'cookie' => 'cookies',
        'corpus' => 'corpuses',
        'cow' => 'cows',
        'criterion' => 'criteria',
        'curriculum' => 'curricula',
        'curve' => 'curves',
        'demo' => 'demos',
        'die' => 'dice',
        'domino' => 'dominoes',
        'echo' => 'echoes',
        'emphasis' => 'emphases',
        'epoch' => 'epochs',
        'foe' => 'foes',
        'foot' => 'feet',
        'fungus' => 'fungi',
        'ganglion' => 'ganglions',
        'gas' => 'gases',
        'genie' => 'genies',
        'genus' => 'genera',
        'goose' => 'geese',
        'graffito' => 'graffiti',
        'grave' => 'graves',
        'hippopotamus' => 'hippopotami',
        'hoax' => 'hoaxes',
        'hoof' => 'hoofs',
        'human' => 'humans',
        'iris' => 'irises',
        'larva' => 'larvae',
        'leaf' => 'leaves',
        'lens' => 'lenses',
        'loaf' => 'loaves',
        'man' => 'men',
        'medium' => 'media',
        'memorandum' => 'memoranda',
        'money' => 'monies',
        'mongoose' => 'mongooses',
        'motto' => 'mottoes',
        'move' => 'moves',
        'mythos' => 'mythoi',
        'neurosis' => 'neuroses',
        'niche' => 'niches',
        'niveau' => 'niveaux',
        'nucleus' => 'nuclei',
        'numen' => 'numina',
        'nursery' => 'nurseries',
        'oasis' => 'oases',
        'occiput' => 'occiputs',
        'octopus' => 'octopuses',
        'opus' => 'opuses',
        'ox' => 'oxen',
        'passerby' => 'passersby',
        'penis' => 'penises',
        'person' => 'people',
        'plateau' => 'plateaux',
        'runner-up' => 'runners-up',
        'safe' => 'safes',
        'save' => 'saves',
        'sex' => 'sexes',
        'sieve' => 'sieves',
        'soliloquy' => 'soliloquies',
        'son-in-law' => 'sons-in-law',
        'stadium' => 'stadiums',
        'syllabus' => 'syllabi',
        'testis' => 'testes',
        'thief' => 'thieves',
        'tooth' => 'teeth',
        'tornado' => 'tornadoes',
        'trilby' => 'trilbys',
        'turf' => 'turfs',
        'valve' => 'valves',
        'volcano' => 'volcanoes',
        'wave' => 'waves',
        'zombie' => 'zombies',
    ];

    private const PLURAL_UNINFLECTED = [
        // Doctrine "default" uninflected list
        '\w+media', 'advice', 'aircraft', 'amoyese', 'art', 'audio', 'baggage', 'bison', 'borghese',
        'bream', 'breeches', 'britches', 'buffalo', 'butter', 'cantus', 'carp', 'cattle', 'chassis',
        'clippers', 'clothing', 'coal', 'cod', 'coitus', 'compensation', 'congoese', 'contretemps',
        'coreopsis', 'corps', 'cotton', 'data', 'debris', 'deer', 'diabetes', 'djinn', 'education',
        'eland', 'elk', 'emoji', 'equipment', 'evidence', 'faroese', 'feedback', 'fish', 'flounder',
        'flour', 'foochowese', 'food', 'furniture', 'gallows', 'genevese', 'genoese', 'gilbertese',
        'gold', 'headquarters', 'herpes', 'hijinks', 'homework', 'hottentotese', 'impatience',
        'information', 'innings', 'jackanapes', 'jeans', 'jedi', 'kin', 'kiplingese', 'knowledge',
        'kongoese', 'leather', 'love', 'lucchese', 'luggage', 'mackerel', 'Maltese', 'management',
        'metadata', 'mews', 'money', 'moose', 'mumps', 'music', 'nankingese', 'news', 'nexus',
        'niasese', 'nutrition', 'offspring', 'oil', 'patience', 'pekingese', 'piedmontese', 'pincers',
        'pistoiese', 'plankton', 'pliers', 'pokemon', 'police', 'polish', 'portuguese', 'proceedings',
        'progress', 'rabies', 'rain', 'research', 'rhinoceros', 'rice', 'salmon', 'sand', 'sarawakese',
        'scissors', 'sea[- ]bass', 'series', 'shavese', 'shears', 'sheep', 'siemens', 'silk', 'sms',
        'soap', 'social media', 'spam', 'species', 'staff', 'sugar', 'swine', 'talent', 'toothpaste',
        'traffic', 'travel', 'trousers', 'trout', 'tuna', 'us', 'vermontese', 'vinegar', 'weather',
        'wenchowese', 'wheat', 'whiting', 'wildebeest', 'wood', 'wool', 'yengeese',
        // plural-only additions
        'people', 'trivia', '\w+ware$', 'media',
    ];

    static function pluralize(string $word): string
    {
        if ($word === '') {
            return '';
        }

        if (preg_match('/^(?:' . implode('|', self::PLURAL_UNINFLECTED) . ')$/i', $word) === 1) {
            return $word;
        }

        $lower = strtolower($word);

        if (isset(self::PLURAL_IRREGULAR[$lower])) {
            $to = self::PLURAL_IRREGULAR[$lower];

            return $lower[0] !== $word[0] ? strtoupper($to[0]) . substr($to, 1) : $to;
        }

        foreach (self::PLURAL as [$pattern, $replacement]) {
            $regex = "/$pattern/i";

            if (preg_match($regex, $word) === 1) {
                return (string) preg_replace($regex, $replacement, $word);
            }
        }

        return $word;
    }
}
