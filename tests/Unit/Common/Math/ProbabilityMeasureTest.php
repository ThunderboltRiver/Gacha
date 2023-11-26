<?php

namespace Tests\Unit;

use App\Common\Math\ProbabilityMeasure;
use App\Common\Math\Ratio;
use PHPUnit\Framework\TestCase;

class ProbabilityMeasureTest extends TestCase
{
    /**
     * @dataProvider probMeasureEqualityProvider
     * @test
     */
    public function 確率測度の等価性(bool $expected, ProbabilityMeasure $probMeasure1, ProbabilityMeasure $probMeasure2): void
    {
        $this->assertSame($expected, $probMeasure1->isEqualTo($probMeasure2));
    }

    /**
     * @dataProvider probMeasureAndSamplePointProvider
     * @test
     */
    public function 各標本点の値の範囲のテスト(Ratio $expected, ProbabilityMeasure $probMeasure, string $samplePoint): void
    {
        $this->assertTrue($expected->isEqualTo($probMeasure->probabilityAt($samplePoint)));
    }

    /**
     * @dataProvider probMeasureAndConditionProvider
     * @test
     */
    public function 事象の値のテスト(Ratio $expected, ProbabilityMeasure $probMeasure, callable $condition): void
    {
        $this->assertTrue($expected->isEqualTo($probMeasure->probabilityWhere($condition)));
    }

    /**
     * @dataProvider relativeProbMeasureProvider
     * @test
     */
    public function 相対確率測度の等価性のテスト(ProbabilityMeasure $expected, ProbabilityMeasure $probMeasure, callable $condition): void
    {
        $this->assertTrue($expected->isEqualTo($probMeasure->relativeWhere($condition)));
    }

    public static function probMeasureEqualityProvider(): array
    {
        return [
            '標本空間とその標本に対する重みが等しいなら等しい' => [
                true,
                new ProbabilityMeasure([
                    'sample1' => new Ratio(1, 1),
                    'sample2' => new Ratio(2, 1),
                    'sample3' => new Ratio(3, 1),
                ]),
                new ProbabilityMeasure([
                    'sample1' => new Ratio(2, 2),
                    'sample2' => new Ratio(2, 1),
                    'sample3' => new Ratio(6, 2),
                ])
            ],
            '標本空間が異なるなら等しくない' => [
                false,
                new ProbabilityMeasure([
                    'sample1' => new Ratio(1, 1),
                    'sample2' => new Ratio(2, 1),
                    'sample3' => new Ratio(3, 1),
                ]),
                new ProbabilityMeasure([
                    'sample1' => new Ratio(1, 1),
                    'sample2' => new Ratio(2, 1),
                ])
            ],
            '標本空間は同じだが、標本に対する重みが異なるなら等しくない' => [
                false,
                new ProbabilityMeasure([
                    'sample1' => new Ratio(1, 1),
                    'sample2' => new Ratio(2, 1),
                    'sample3' => new Ratio(3, 1),
                ]),
                new ProbabilityMeasure([
                    'sample1' => new Ratio(1, 1),
                    'sample2' => new Ratio(2, 1),
                    'sample3' => new Ratio(4, 1),
                ])
            ],
        ];
    }

    public static function probMeasureAndSamplePointProvider(): array
    {
        $probMeasure = new ProbabilityMeasure([
            'sample1' => new Ratio(1, 1),
            'sample2' => new Ratio(2, 1),
            'sample3' => new Ratio(3, 1),
        ]);

        $relativeMesure = $probMeasure->relativeWhere(
            fn (Ratio $probValue, string $samplePoint): bool => $samplePoint === 'sample1' || $samplePoint === 'sample2'
        );
        return [
            '標本空間の重みの集合が{1,2,3}のときのsample1の値は1/6' => [new Ratio(1, 6), $probMeasure, 'sample1'],
            '標本空間の重みの集合が{1,2,3}のときのsample2の値2/6' => [new Ratio(2, 6), $probMeasure, 'sample2'],
            '標本空間の重みの集合が{1,2,3}のときのsample3の値1/2' => [new Ratio(1, 2), $probMeasure, 'sample3'],
            'sapmle1とsample2の相対確率測度のsample1の値は1/3' => [new Ratio(1, 3), $relativeMesure, 'sample1'],
        ];
    }

    public static function probMeasureAndConditionProvider(): array
    {
        $probMeasure = new ProbabilityMeasure([
            'sample1' => new Ratio(1, 1),
            'sample2' => new Ratio(2, 1),
            'sample3' => new Ratio(3, 1),
        ]);
        return [
            '標本空間の重みの集合が{1,2,3}のときのsample1とsample2の合計値は1/2' => [
                new Ratio(1, 2),
                $probMeasure,
                function (Ratio $probValue, string $samplePoint): bool {
                    return $samplePoint === 'sample1' || $samplePoint === 'sample2';
                }
            ],
            '全標本の合計値は1' => [
                Ratio::one(),
                $probMeasure,
                function (Ratio $probValue, string $samplePoint): bool {
                    return true;
                }
            ],
        ];
    }

    public static function relativeProbMeasureProvider(): array
    {
        $probMeasure = new ProbabilityMeasure([
            'sample1' => new Ratio(1, 1),
            'sample2' => new Ratio(2, 1),
            'sample3' => new Ratio(3, 1),
        ]);
        return [
            '標本空間の重みの集合が{1,2,3}のときのsample1とsample2における相対確率測度' => [
                new ProbabilityMeasure([
                    'sample1' => new Ratio(1, 1),
                    'sample2' => new Ratio(2, 1),
                ]),
                $probMeasure,
                function (Ratio $probValue, string $samplePoint): bool {
                    return $samplePoint === 'sample1' || $samplePoint === 'sample2';
                }
            ],
        ];
    }
}
