<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

use \System\Collections\IEnumerable;
use \System\Linq\Enumerable;


function joinPersonKeySelectorFunc(Person $person) : string {
    return $person->Name;
}

function joinPetKeySelectorFunc(Pet $pet) : string {
    return $pet->Owner->Name;
}

function joinResultSelectorFunc(Person $person, Pet $pet) : string {
    return sprintf('Owner: %s; Pet: %s',
                   $person->Name,
                   $pet->Name);
}

class JoinPersonKeySelectorClass {
    public function __invoke(Person $person) {
        return joinPersonKeySelectorFunc($person);
    }
}

class JoinPetKeySelectorClass {
    public function __invoke(Pet $pet) {
        return joinPetKeySelectorFunc($pet);
    }
}

class JoinResultSelectorClass {
    public function __invoke(Person $person, Pet $pet) {
        return joinResultSelectorFunc($person, $pet);
    }
}

/**
 * @see \System\Collection\IEnumerable::join()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class JoinTests extends TestCaseBase {
    /**
     * Creates the data / elements for the tests.
     *
     * @return array The data.
     */
    protected function createElements() : array {
        $persons = array(new Person("Tanja"),
                         new Person("Marcel"),
                         new Person("Yvonne"),
                         new Person("Josefine"));

        $pets = array(new Pet("Gina"     , $persons[1]),
                      new Pet("Schnuffi" , $persons[1]),
                      new Pet("Schnuffel", $persons[2]),
                      new Pet("WauWau"   , $persons[0]),
                      new Pet("Lulu"     , $persons[3]),
                      new Pet("Asta"     , $persons[1]));

        return [$persons, $pets];
    }

    /**
     * Creates selectors for the tests.
     *
     * @return array The list of selectors.
     */
    protected function createSelectors() : array {
        return [
            [
                function(Person $person) {
                    return joinPersonKeySelectorFunc($person);
                },
                function(Pet $pet) {
                    return joinPetKeySelectorFunc($pet);
                },
                function(Person $person, Pet $pet) {
                    return joinResultSelectorFunc($person, $pet);
                },
            ],
            [
                'joinPersonKeySelectorFunc',
                'joinPetKeySelectorFunc',
                'joinResultSelectorFunc',
            ],
            [
                '\joinPersonKeySelectorFunc',
                '\joinPetKeySelectorFunc',
                '\joinResultSelectorFunc',
            ],
            [
                array($this, 'personKeySelectorMethod1'),
                array($this, 'petKeySelectorMethod1'),
                array($this, 'resultSelectorMethod1'),
            ],
            [
                array(static::class, 'personKeySelectorMethod2'),
                array(static::class, 'petKeySelectorMethod2'),
                array(static::class, 'resultSelectorMethod2'),
            ],
            [
                new JoinPersonKeySelectorClass(),
                new JoinPetKeySelectorClass(),
                new JoinResultSelectorClass(),
            ],
            [
                '$person => \joinPersonKeySelectorFunc($person)',
                '$pet => \joinPetKeySelectorFunc($pet)',
                '$person, $pet => joinResultSelectorFunc($person, $pet)',
            ],
            [
                '($person) => joinPersonKeySelectorFunc($person)',
                '($pet) => joinPetKeySelectorFunc($pet)',
                '($person, $pet) => \joinResultSelectorFunc($person, $pet)',
            ],
            [
                '$person => return $person->Name;',
                '$pet => return $pet->Owner->Name;',
                '$person, $pet => return sprintf(\'Owner: %s; Pet: %s\', $person->Name, $pet->Name);',
            ],
            [
                '($person) => return $person->Name;',
                '($pet) => return $pet->Owner->Name;',
                '($person, $pet) => return sprintf(\'Owner: %s; Pet: %s\', $person->Name, $pet->Name);',
            ],
            [
                '$person => { return $person->Name; }',
                '$pet => { return $pet->Owner->Name; }',
                '$person, $pet => { return sprintf(\'Owner: %s; Pet: %s\', $person->Name, $pet->Name); }',
            ],
            [
                '($person) => { return $person->Name; }',
                '($pet) => { return $pet->Owner->Name; }',
                '($person, $pet) => { return sprintf(\'Owner: %s; Pet: %s\', $person->Name, $pet->Name); }',
            ],
            [
                '$person => {
return $person->Name;
}',
                '$pet => {
return $pet->Owner->Name;
}',
                '$person, $pet => {
return sprintf(\'Owner: %s; Pet: %s\', $person->Name, $pet->Name);
}',
            ],
            [
                '($person) => {
return $person->Name;
}',
                '($pet) => {
return $pet->Owner->Name;
}',
                '($person, $pet) => {
return sprintf(\'Owner: %s; Pet: %s\', $person->Name, $pet->Name);
}',
            ],
        ];
    }

    /**
     * Executes tests for a person sequence.
     *
     * @param callable $personSeqFactory The factory that returns / produces the person sequence.
     */
    protected function executeForPersonSequence(callable $personSeqFactory) {
        list($persons, $pets) = $this->createElements();

        foreach ($this->createSelectors() as $selectors) {
            list ($personKeySelector, $petKeySelector, $resultSelector) = $selectors;

            $petLists   = static::sequenceListFromArray($pets);
            $petLists[] = $pets;

            foreach ($petLists as $petSeq) {
                /* @var IEnumerable $personSeq */

                $personSeq = $personSeqFactory($persons);

                $joined = $personSeq->join($petSeq,
                                           $personKeySelector, $petKeySelector, $resultSelector);

                $items = static::sequenceToArray($joined);

                $this->assertEquals(6, count($items));
                $this->assertEquals('Owner: Tanja; Pet: WauWau', $items[0]);
                $this->assertEquals('Owner: Marcel; Pet: Gina', $items[1]);
                $this->assertEquals('Owner: Marcel; Pet: Schnuffi', $items[2]);
                $this->assertEquals('Owner: Marcel; Pet: Asta', $items[3]);
                $this->assertEquals('Owner: Yvonne; Pet: Schnuffel', $items[4]);
                $this->assertEquals('Owner: Josefine; Pet: Lulu', $items[5]);
            }
        }
    }

    public function personKeySelectorMethod1(Person $person) {
        return joinPersonKeySelectorFunc($person);
    }

    public static function personKeySelectorMethod2(Person $person) {
        return joinPersonKeySelectorFunc($person);
    }

    public function petKeySelectorMethod1(Pet $pet) {
        return joinPetKeySelectorFunc($pet);
    }

    public static function petKeySelectorMethod2(Pet $pet) {
        return joinPetKeySelectorFunc($pet);
    }

    public function resultSelectorMethod1(Person $person, Pet $pet) {
        return joinResultSelectorFunc($person, $pet);
    }

    public static function resultSelectorMethod2(Person $person, Pet $pet) {
        return joinResultSelectorFunc($person, $pet);
    }

    public function testArray() {
        $this->executeForPersonSequence(function(array $persons) {
            return Enumerable::create($persons);
        });
    }

    public function testGenerator() {
        $this->executeForPersonSequence(function(array $persons) {
            $createGenerator = function() use ($persons) {
                foreach ($persons as $p) {
                    yield $p;
                }
            };

            return Enumerable::create($createGenerator());
        });
    }

    public function testIterator() {
        $this->executeForPersonSequence(function(array $persons) {
            return Enumerable::create(new ArrayIterator($persons));
        });
    }
}
