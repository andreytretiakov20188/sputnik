<?php

namespace Tests\Unit;

use Generator;
use Sputnik\Models\Events\Event;
use Sputnik\Models\FlightProgram;
use Sputnik\Models\Operations\Operation;
use Tests\TestCase;

class FlightProgramTest extends TestCase
{
    public function testFromJson()
    {
        // arrange
        $json = '{"startUp": 1555016400,"operations":[]}';

        // act
        $result = FlightProgram::fromJson($json);

        // assert
        self::assertEquals('1555016400', $result->getStartUp());
        self::assertInstanceOf(Generator::class, $result->getOperations());
        self::assertEquals([], $this->iterator2array($result->getOperations()));
    }

    public function testFromJsonOperations()
    {
        // arrange
        $json = $this->readFixture('flight_program/default.json');

        // act
        $result = FlightProgram::fromJson($json);

        // assert
        self::assertEquals('1555016400', $result->getStartUp());
        self::assertInstanceOf(Generator::class, $result->getOperations());
        self::assertEquals([
            Operation::createOperation(1, 0, "coolingSystemPowerPct", 30, 2),
            Operation::createOperation(2, 10, "radioPowerDbm", 50, 1, false),
            Operation::createOperation(3, 15, "orientationZenithAngleDeg", 270, 10),
            Operation::createOperation(4, 15, "orientationAzimuthAngleDeg", 0, 10),
        ], $this->iterator2array($result->getOperations()));
    }

    /**
     * @expectedException Sputnik\Exceptions\InvalidFlightProgram
     * @expectedExceptionMessage Invalid flight program: json
     */
    public function testFromJsonInvalidJson()
    {
        // arrange
        $json = '{2"startUp": 1555016400,"operations":[]}';

        // act
        $result = FlightProgram::fromJson($json);

        // assert
        self::assertInstanceOf(FlightProgram::class, $result);
        self::assertEquals('1555016400', $result->getStartUp());
        self::assertInstanceOf(Generator::class, $result->getOperations());
        self::assertEquals([], $this->iterator2array($result->getOperations()));
    }

    public static function fromJsonInvalidStartUpDataProvider()
    {
        return [
            ['{"operations":[]}'],
            ['{"startUp": -1,"operations":[]}'],
            ['{"startUp": "string","operations":[]}'],
            ['{"startUp": 4294967296,"operations":[]}'],
        ];
    }

    /**
     * @dataProvider fromJsonInvalidStartUpDataProvider
     *
     * @param string $json
     *
     * @expectedException Sputnik\Exceptions\InvalidFlightProgram
     * @expectedExceptionMessage Invalid flight program: startUp
     */
    public function testFromJsonInvalidStartUp(string $json)
    {
        // act
        FlightProgram::fromJson($json);
    }

    public static function fromJsonInvalidOperationsDataProvider()
    {
        return [
            ['{"startUp": 1555016400}'],
            ['{"startUp": 1555016400, "operations":"string"}'],
            ['{"startUp": 1555016400,"operations":{}}'],
        ];
    }

    /**
     * @dataProvider fromJsonInvalidOperationsDataProvider
     *
     * @param string $json
     *
     * @expectedException Sputnik\Exceptions\InvalidFlightProgram
     * @expectedExceptionMessage Invalid flight program: operations
     */
    public function testFromJsonInvalidOperations(string $json)
    {
        // act
        FlightProgram::fromJson($json);
    }

    public function testCreateSchedule()
    {
        // arrange
        $json = $this->readFixture('flight_program/default.json');
        $operations = [
            Operation::createOperation(1, 0, "coolingSystemPowerPct", 30, 2),
            Operation::createOperation(2, 10, "radioPowerDbm", 50, 1, false),
            Operation::createOperation(3, 15, "orientationZenithAngleDeg", 270, 10),
            Operation::createOperation(4, 15, "orientationAzimuthAngleDeg", 0, 10),
        ];

        $flightProgram = FlightProgram::fromJson($json);

        // act
        $result = $flightProgram->createSchedule();

        // assert
        self::assertEquals([
            '1555016400' => [
                'start_operation' => [
                    Event::createEvent(1555016400, Event::TYPE_START_OPERATION, $operations[0]),
                ],
            ],
            '1555016402' => [
                'check_operation_results' => [
                    Event::createEvent(1555016402, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[0]),
                ],
            ],
            '1555016410' => [
                'start_operation' => [
                    Event::createEvent(1555016410, Event::TYPE_START_OPERATION, $operations[1]),
                ],
            ],
            '1555016411' => [
                'check_operation_results' => [
                    Event::createEvent(1555016411, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[1]),
                ],
            ],
            '1555016415' => [
                'start_operation' => [
                    Event::createEvent(1555016415, Event::TYPE_START_OPERATION, $operations[2]),
                    Event::createEvent(1555016415, Event::TYPE_START_OPERATION, $operations[3]),
                ],
            ],
            '1555016425' => [
                'check_operation_results' => [
                    Event::createEvent(1555016425, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[2]),
                    Event::createEvent(1555016425, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[3]),
                ],
            ],
        ], $result);
    }

    public function testGetSchedule()
    {
        // arrange
        $json = $this->readFixture('flight_program/default.json');
        $operations = [
            Operation::createOperation(1, 0, "coolingSystemPowerPct", 30, 2),
            Operation::createOperation(2, 10, "radioPowerDbm", 50, 1, false),
            Operation::createOperation(3, 15, "orientationZenithAngleDeg", 270, 10),
            Operation::createOperation(4, 15, "orientationAzimuthAngleDeg", 0, 10),
        ];

        $flightProgram = FlightProgram::fromJson($json);

        // act
        $flightProgram->createSchedule();
        $result = $flightProgram->getSchedule();

        // assert
        self::assertEquals([
            '1555016400' => [
                'start_operation' => [
                    Event::createEvent(1555016400, Event::TYPE_START_OPERATION, $operations[0]),
                ],
            ],
            '1555016402' => [
                'check_operation_results' => [
                    Event::createEvent(1555016402, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[0]),
                ],
            ],
            '1555016410' => [
                'start_operation' => [
                    Event::createEvent(1555016410, Event::TYPE_START_OPERATION, $operations[1]),
                ],
            ],
            '1555016411' => [
                'check_operation_results' => [
                    Event::createEvent(1555016411, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[1]),
                ],
            ],
            '1555016415' => [
                'start_operation' => [
                    Event::createEvent(1555016415, Event::TYPE_START_OPERATION, $operations[2]),
                    Event::createEvent(1555016415, Event::TYPE_START_OPERATION, $operations[3]),
                ],
            ],
            '1555016425' => [
                'check_operation_results' => [
                    Event::createEvent(1555016425, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[2]),
                    Event::createEvent(1555016425, Event::TYPE_CHECK_OPERATION_RESULTS, $operations[3]),
                ],
            ],
        ], $result);
    }
}