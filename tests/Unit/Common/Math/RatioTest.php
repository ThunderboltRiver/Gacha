<?php

namespace Tests\Unit\Common\Math;

use PHPUnit\Framework\TestCase;
use App\Common\Math\Ratio;

class RatioTest extends TestCase
{
    /**
     * @dataProvider ratioEqualityProvider
     * @test
     */
    public function 比率の等価性(bool $expected, Ratio $ratio1, Ratio $ratio2): void
    {
        $this->assertSame($expected, $ratio1->isEqualTo($ratio2));
    }

    /**
     * @dataProvider ratioPlussProvider
     * @test
     */
    public function 比率の足し算(Ratio $expected, Ratio $ratio1, Ratio $ratio2): void
    {
        $this->assertTrue($expected->isEqualTo($ratio1->pluss($ratio2)));
    }
    /**
     * @dataProvider ratioDivideProvider
     * @test
     */
    public function 比率の割り算(Ratio $expected, Ratio $ratio1, Ratio $ratio2): void
    {
        $this->assertTrue($expected->isEqualTo($ratio1->dividedBy($ratio2)));
    }

    /**
     * @dataProvider ratioGraterThanProvider
     * @test
     */
    public function 比率の大なりによる比較(bool $expected, Ratio $ratio1, Ratio $ratio2): void
    {
        $this->assertSame($expected, $ratio1->isGreaterThan($ratio2));
    }

    /**
     * @dataProvider ratioLessThanProvider
     * @test
     */
    public function 比率の小なりによる比較(bool $expected, Ratio $ratio1, Ratio $ratio2): void
    {
        $this->assertSame($expected, $ratio1->isLessThan($ratio2));
    }

    /**
     * @dataProvider ratioApproximationProvider
     * @test
     */
    public function 比率の近似値(float $expected, Ratio $ratio): void
    {
        $this->assertSame($expected, $ratio->calcApproximation());
    }

    /**
     * @dataProvider ratioArrayProvider
     * @test
     */
    public function 比率の配列から合計値を計算できる(Ratio $expected, array $ratioArray): void
    {
        $this->assertTrue($expected->isEqualTo(Ratio::calcSum($ratioArray)));
    }

    public static function ratioEqualityProvider(): array
    {
        return [
            '1:2と1:2は等しい' => [true, new Ratio(1, 2), new Ratio(1, 2)],
            '1:2と1:3は等しくない' => [false, new Ratio(1, 2), new Ratio(1, 3)],
            '1:2と2:4は等しい' => [true, new Ratio(1, 2), new Ratio(2, 4)],
        ];
    }

    public static function ratioPlussProvider(): array
    {
        return [
            '1:2と1:2を足すと2:2になる' => [new Ratio(2, 2), new Ratio(1, 2), new Ratio(1, 2)],
            '1:2と1:3を足すと5:6になる' => [new Ratio(5, 6), new Ratio(1, 2), new Ratio(1, 3)],
        ];
    }

    public static function ratioDivideProvider(): array
    {
        return [
            '1:2を1:2で割ると1:1になる' => [new Ratio(1, 1), new Ratio(1, 2), new Ratio(1, 2)],
            '1:2を1:3で割ると3:2になる' => [new Ratio(3, 2), new Ratio(1, 2), new Ratio(1, 3)],
        ];
    }

    public static function ratioGraterThanProvider(): array
    {
        return [
            '1:2は1:3より大きい' => [true, new Ratio(1, 2), new Ratio(1, 3)],
            '1:2は1:2より大きくない' => [false, new Ratio(1, 2), new Ratio(1, 2)],
            '1:3は1:2より大きくない' => [false, new Ratio(1, 3), new Ratio(1, 2)],
        ];
    }

    public static function ratioLessThanProvider(): array
    {
        return [
            '1:3は1:2より小さい' => [true, new Ratio(1, 3), new Ratio(1, 2)],
            '1:2は1:2より小さくない' => [false, new Ratio(1, 2), new Ratio(1, 2)],
            '1:2は1:3より小さくない' => [false, new Ratio(1, 2), new Ratio(1, 3)],
        ];
    }

    public static function ratioApproximationProvider(): array
    {
        return [
            '1:2の値は0.5' => [0.5, new Ratio(1, 2)],
            '1:3の値は0.3333...' => [0.3333333333333333, new Ratio(1, 3)],
            '2:3の値は0.6666...' => [0.6666666666666666, new Ratio(2, 3)],
        ];
    }

    public static function ratioArrayProvider(): array
    {
        return [
            '1:2と1:3の合計は5:6' => [new Ratio(5, 6), [new Ratio(1, 2), new Ratio(1, 3)]],
            '1:2と1:3と1:6の合計は1:1' => [new Ratio(1, 1), [new Ratio(1, 2), new Ratio(1, 3), new Ratio(1, 6)]],
            '1:2と1:3と1:6と1:12の合計は13:12' => [new Ratio(13, 12), [new Ratio(1, 2), new Ratio(1, 3), new Ratio(1, 6), new Ratio(1, 12)]],
        ];
    }
}
