<?php

namespace App\Common\Math;

/**
 * 確率測度を表すクラス
 */
class ProbabilityMeasure
{
    /**
     * @var array<string, Ratio> $sampleSpace 標本空間。標本点とその重みのペアの配列
     */
    public function __construct(
        private readonly array $sampleSpace
    ) {
    }

    /**
     * 確率測度の等価性
     * 標本空間とその標本に対する重みが等しいなら同じ確率測度とみなす
     * @param ProbabilityMeasure $other
     * @return bool
     */
    public function isEqualTo(ProbabilityMeasure $other): bool
    {
        $thisAfterOtherRemoved = array_diff_key($this->sampleSpace, $other->sampleSpace);
        $otherAfterthisRemoved = array_diff_key($other->sampleSpace, $this->sampleSpace);
        return empty($thisAfterOtherRemoved) && empty($otherAfterthisRemoved) // 標本空間の標本点が一致するかどうかを調べる
            && array_reduce( // 標本空間の各標本点について、重みが等しいかどうかを調べる
                array_keys($this->sampleSpace),
                fn (bool $carry, string $samplePoint) => $carry && $this->sampleSpace[$samplePoint]->isEqualTo($other->sampleSpace[$samplePoint]),
                true
            );
    }

    /**
     * 標本点の確率値を返す。
     * @param string $samplePoint 標本点
     * @return Ratio 標本点の確率値
     */
    public function probabilityAt(string $samplePoint): Ratio
    {
        return $this->sampleSpace[$samplePoint]->dividedBy(Ratio::calcSum(array_values($this->sampleSpace)));
    }

    /**
     * 事象の確率値を返す
     * @param callable $condition fn (string $samplePoint, Ratio $probability): bool というシグネチャの関数
     * @return Ratio   事象の確率値
     */
    public function probabilityWhere(callable $condition): Ratio
    {
        // 指定された条件に一致する標本点の重みの合計値を計算する
        $sumWhere = Ratio::calcSum(array_values(array_filter($this->sampleSpace, $condition, ARRAY_FILTER_USE_BOTH)));
        return $sumWhere->dividedBy(Ratio::calcSum(array_values($this->sampleSpace)));
    }

    /**
     * 相対確率測度を返す
     * @param callable $condition fn (string $samplePoint, Ratio $probability): bool というシグネチャの関数
     * @return ProbabilityMeasure 相対確率測度
     */
    public function relativeWhere(callable $condition): ProbabilityMeasure
    {
        return new ProbabilityMeasure(array_filter($this->sampleSpace, $condition, ARRAY_FILTER_USE_BOTH));
    }
}
