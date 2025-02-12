<?php

namespace MatanYadaev\EloquentSpatial\Tests\Objects;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use InvalidArgumentException;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Tests\TestCase;
use MatanYadaev\EloquentSpatial\Tests\TestModels\TestPlace;

class PointTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_stores_point(): void
    {
        /** @var TestPlace $testPlace */
        $testPlace = TestPlace::factory()->create([
          'point' => new Point(180, 0),
        ]);

        $this->assertTrue($testPlace->point instanceof Point);
        $this->assertEquals(180, $testPlace->point->latitude);
        $this->assertEquals(0, $testPlace->point->longitude);

        $this->assertDatabaseCount($testPlace->getTable(), 1);
    }

    /** @test */
    public function it_stores_point_srid(): void
    {
        /** @var TestPlace $testPlace */
        $testPlace = TestPlace::factory()->create([
          'point' => new Point(30.0526012, 31.2772677, 4326),
        ]);

        $this->assertTrue($testPlace->point instanceof Point);
        $this->assertEquals(30.0526012, $testPlace->point->latitude);
        $this->assertEquals(31.2772677, $testPlace->point->longitude);
        $this->assertEquals(4326, $testPlace->point->getSrid());

        $this->assertDatabaseCount($testPlace->getTable(), 1);
        $this->assertEquals(1, TestPlace::whereRaw('ST_SRID(point) = 4326')->count());
    }

    /** @test */
    public function it_stores_point_from_json(): void
    {
        /** @var TestPlace $testPlace */
        $testPlace = TestPlace::factory()->create([
          'point' => Point::fromJson('{"type":"Point","coordinates":[0,180]}'),
        ]);

        $this->assertTrue($testPlace->point instanceof Point);
        $this->assertEquals(180, $testPlace->point->latitude);
        $this->assertEquals(0, $testPlace->point->longitude);

        $this->assertDatabaseCount($testPlace->getTable(), 1);
    }

    /** @test */
    public function it_stores_point_from_json_with_srid(): void
    {
        /** @var TestPlace $testPlace */
        $testPlace = TestPlace::factory()->create([
          'point' => Point::fromJson('{"type":"Point","coordinates":[31.2772677,30.0526012]}', 4326),
        ]);

        $this->assertTrue($testPlace->point instanceof Point);
        $this->assertEquals(30.0526012, $testPlace->point->latitude);
        $this->assertEquals(31.2772677, $testPlace->point->longitude);
        $this->assertEquals(4326, $testPlace->point->getSrid());

        $this->assertDatabaseCount($testPlace->getTable(), 1);
        $this->assertEquals(1, TestPlace::whereRaw('ST_SRID(point) = 4326')->count());

    }

    /** @test */
    public function it_generates_point_geo_json(): void
    {
        $point = new Point(180, 0);

        $this->assertEquals('{"type":"Point","coordinates":[0,180]}', $point->toJson());
    }

    /** @test */
    public function it_throws_exception_when_generating_point_from_geo_json_without_coordinates(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Point::fromJson('{"type":"Point","coordinates":[]}');
    }
}
