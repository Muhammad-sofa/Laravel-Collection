<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Data\Person;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3, 4]);
        //index harus sama kalau tidak peduli dengan index yang penting isi sama pakai assertEqualsCanonicalizing
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach($collection as $key => $value){
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        $this->assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Sofa"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("Sofa")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ["Sofa", "Yuliansyah"],
            ["Affandi", "Susanto"]
        ]);
        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . ' ' . $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Sofa Yuliansyah"),
            new Person("Affandi Susanto"),
        ], $result->all());
    }

     public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "Sofa",
                "department" => "IT"
            ],
            [
                "name" => "Tasya",
                "department" => "IT"
            ],
            [
                "name" => "Budi",
                "department" => "HR"
            ]
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["department"] => $person["name"]
            ];
        });

        $this->assertEquals([
            "IT" => collect(["Sofa", "Tasya"]),
            "HR" => collect(["Budi"])
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
            collect([3,6]),
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name", "country"]);
        $collection2 = collect(["Sofa", "Indonesia"]);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing([
            "name" => "Sofa",
            "country" => "Indonesia"
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "Sofa",
                "hobies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Affan",
                "hobies" => ["Travelling", "Badminton"]
            ]
        ]);
        $result = $collection->flatMap(function ($item) {
            $hobies = $item["hobies"];
            return $hobies;
        });

        $this->assertEqualsCanonicalizing(["Coding", "Gaming", "Travelling", "Badminton"], $result->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(["Sofa", "Yuliansyah", "Yuliansyah"]);
        $this->assertEquals("Sofa-Yuliansyah-Yuliansyah", $collection->join("-"));
        $this->assertEquals("Sofa-Yuliansyah_Yuliansyah", $collection->join("-", "_"));
    }

    public function testFilter()
    {
        $collection = collect([
            "Sofa" => 100,
            "Affan" => 90,
            "Joko" => 80
        ]);

        $result = $collection->filter(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            "Sofa" => 100,
            "Affan" => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());
    }
}
