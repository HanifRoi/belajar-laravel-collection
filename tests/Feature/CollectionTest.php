<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;
use function PHPUnit\Framework\assertEqualsWithDelta;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1,2,3]);
        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        foreach($collection as $key => $value){
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1,2,3);
        $this->assertEquals([1,2,3],$collection->all());


        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1,2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1,2,3]);
        $result = $collection->map(function ($item){
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing([2,4,6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(['Hanif']);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person('Hanif')], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['Hanif', 'Ganteng'],
            ['Roihan', 'Pintar']
        ]);
        $result = $collection->mapSpread(function ($firstname, $lastname) {
            $fullname = $firstname . ' ' . $lastname;
            return new Person($fullname);
        });

        $this->assertEquals([
            new Person('Hanif Ganteng'),
            new Person('Roihan Pintar')
            ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                'name' => 'Hanif',
                'departement' => 'IT'
            ],
            [
                'name' => 'Roihan',
                'departement' => 'IT'
            ],
            [
                'name' => 'Yogi',
                'departement' => 'HR'
            ]
        ]);

        $result = $collection->mapToGroups(function  ($item) {
            return[
                $item['departement'] => $item['name']
            ];
        });

        $this->assertEquals([
            'IT' => collect(['Hanif', 'Roihan']),
            'HR' => collect(['Yogi'])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1,4]),
            collect([2,5]),
            collect([3,6])
        ], $collection3->all());

    }

    public function testConCat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEquals([
            1,2,3,4,5,6
        ], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(['name', 'country']);
        $collection2 = collect(['Hanif', 'Indonesia']);
        $collection3 = $collection1->combine($collection2);

        $this->assertEquals([
            'name' => 'Hanif',
            'country' => 'Indonesia'
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);

        $result = $collection->collapse();
        assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                'name'=> 'Hanif',
                'hobbies'=>['Coding', 'Gaming']
            ],
             [
                'name'=> 'Roihan',
                'hobbies'=>['Coding', 'Gaming']
            ]
        ]);

        $hobbies = $collection->flatMap(function($item){
            return $item['hobbies'];
        });

        $this->assertEquals(['Coding', 'Gaming', 'Coding', 'Gaming'], $hobbies->all());
    }

    public function testPluck()
    {
         $collection = collect([
            [
                'name'=> 'Hanif',
                'hobbies'=>['Coding', 'Gaming']
            ],
             [
                'name'=> 'Roihan',
                'hobbies'=>['Coding', 'Gaming']
            ]
        ]);

        $name = $collection->pluck('name');
        $this->assertEquals(['Hanif', 'Roihan'], $name->all());
    }

    public function testJoin()
    {
        $collection = collect(['Hanif', 'Roihan', 'Ganteng', 'Banget']);

        assertEquals('Hanif-Roihan-Ganteng-Banget', $collection->join('-'));

        assertEquals('Hanif-Roihan-Ganteng_Banget', $collection->join('-', '_'));
    }

    public function testFilter()
    {
        $collection = collect([
            'Hanif'=>100,
            'Roihan'=>90,
            'Budi'=>80
        ]);

        $result = $collection->filter(function ($item, $key) {
            return $item >= 90;
        });

        assertEquals([
            'Hanif'=>100,
            'Roihan'=>90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2,4,6,8,10], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            'Hanif'=>100,
            'Roihan'=>90,
            'Budi'=>80
        ]);

        [$result1, $result2] = $collection->partition(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            'Hanif'=>100,
            'Roihan'=>90
        ], $result1->all());

        $this->assertEquals([
            'Budi'=>80
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(['Hanif', 'Ganteng', 'Roihan']);
        self::assertTrue($collection->contains('Roihan'));
        self::assertTrue($collection->contains(function ($value, $key) {
            return $value == 'Roihan';
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                'name'=>'Hanif',
                'departement'=>'IT'
            ],
             [
                'name'=>'Roihan',
                'departement'=>'IT'
            ],
             [
                'name'=>'Budi',
                'departement'=>'HR'
            ]

        ]);

        $result = $collection->groupBy('departement');

        assertEquals([
            'IT'=> collect([
                [
                    'name'=>'Hanif',
                    'departement'=>'IT'
                ],
                [
                    'name'=>'Roihan',
                    'departement'=>'IT'
                ]
            ]),
            'HR'=>collect([
                [
                    'name'=>'Budi',
                    'departement'=>'HR'
                ]
            ])
        ], $result->all());


        $result = $collection->groupBy(function ($value, $key) {
            return strtolower($value['departement']);
        });
         assertEquals([
            'it'=> collect([
                [
                    'name'=>'Hanif',
                    'departement'=>'IT'
                ],
                [
                    'name'=>'Roihan',
                    'departement'=>'IT'
                ]
            ]),
            'hr'=>collect([
                [
                    'name'=>'Budi',
                    'departement'=>'HR'
                ]
            ])
        ], $result->all());
    }

    public function testSlice()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->slice(3);
        assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->slice(2);
        assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());

        $result = $collection->slice(3, 2);
        assertEqualsCanonicalizing([4,5], $result->all());

        $result = $collection->slice(3, 3);
        assertEqualsCanonicalizing([4,5,6], $result->all());
    }

    public function testTake()
    {
        $collection = collect([1,2,3,1,2,3,1,2,3]);

        $result = $collection->take(3);
        assertEqualsCanonicalizing([1,2,3], $result->all());

        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });
        assertEqualsCanonicalizing([1,2], $result->all());

        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });
        assertEqualsCanonicalizing([1,2], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->skip(3);
        assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->skipUntil(function ($value, $key) {
            return $value == 3;
        });
        assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());

        $result = $collection->skipWhile(function ($value, $key) {
            return $value < 3;
        });
        assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());
    }

    public function testChunk()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);

        $result = $collection->chunk(3);
        $this->assertEqualsCanonicalizing([1,2,3], $result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4,5,6], $result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7,8,9], $result->all()[2]->all());
        $this->assertEqualsCanonicalizing([10], $result->all()[3]->all());
    }

    public function testFirst()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);

        $result = $collection->first();
        assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });
        assertEquals(6, $result);
    }

    public function testLast()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);

        $result = $collection->last();
        assertEquals(10, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 5;
        });
        assertEquals(4, $result);
    }

    public function testRandom()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);

        $result = $collection->random();

        $this->assertTrue(in_array($result, [1,2,3,4,5,6,7,8,9]));

        // $result = $collection->random(5);
        // $this->assertEquals([1,2,3,4,5], $result->all());
    }

    public function testCheckingExistence()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $this->assertTrue($collection->isNotEmpty());
        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->contains(1));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value >= 8;
        }));

    }

    public function testOrdering()
    {
        $collection = collect([1,4,5,6,8,7,9,3,2]);

        $result = $collection->sort();
        assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());
        
        $result = $collection->sortDesc();
        assertEqualsCanonicalizing([9,8,7,6,5,4,3,2,1], $result->all());
    }

    public function testAggregate()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->sum();
        assertEquals(45, $result);

        $result = $collection->average();
        assertEquals(5, $result);

        $result = $collection->min();
        assertEquals(1, $result);

        $result = $collection->max();
        assertEquals(9, $result);
    }

    public function testReduce()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->reduce(function($carry, $item){
            return $carry + $item;
        });

        assertEquals(45, $result);

        //reduce 1 (1,2) = 3
        // reduce 2 (3,3) = 6
        // reduce 3 (6, 4) = 10
        // reduce 4 (10, 5) = 15

    }






}
