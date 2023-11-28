<?php

namespace App\Common\Math;

/**
 * @template T 
 * 確率測度を表すクラス
 */
class ProbabilityMeasure
{
    /**
     * @var array<T, Ratio> $measurableMap 標本点からその重みへの写像。Tは標本点の型
     */
    public function __construct(
        private readonly array $measurableMap
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
        $thisAfterOtherRemoved = array_diff_key($this->measurableMap, $other->measurableMap);
        $otherAfterthisRemoved = array_diff_key($other->measurableMap, $this->measurableMap);
        return empty($thisAfterOtherRemoved) && empty($otherAfterthisRemoved) // 標本空間の標本点が一致するかどうかを調べる
            && array_reduce( // 標本空間の各標本点について、重みが等しいかどうかを調べる
                array_keys($this->measurableMap),
                /** @var T $samplePoint */
                fn (bool $carry, $samplePoint) => $carry && $this->measurableMap[$samplePoint]->isEqualTo($other->measurableMap[$samplePoint]),
                true
            );
    }

    /**
     * 標本点の確率値を返す。
     * @param T $samplePoint 標本点
     * @return Ratio 標本点の確率値
     */
    public function probabilityAt($samplePoint): Ratio
    {
        return $this->measurableMap[$samplePoint]->dividedBy(Ratio::calcSum(array_values($this->measurableMap)));
    }

    /**
     * 事象の確率値を返す
     * @param callable $condition fn (T $samplePoint, Ratio $probability): bool というシグネチャの関数
     * @return Ratio   事象の確率値
     */
    public function probabilityWhere(callable $condition): Ratio
    {
        // 指定された条件に一致する標本点の重みの合計値を計算する
        $sumWhere = Ratio::calcSum(array_values(array_filter($this->measurableMap, $condition, ARRAY_FILTER_USE_BOTH)));
        return $sumWhere->dividedBy(Ratio::calcSum(array_values($this->measurableMap)));
    }

    /**
     * 相対確率測度を返す
     * @param callable $condition fn (T $samplePoint, Ratio $probability): bool というシグネチャの関数
     * @return ProbabilityMeasure 相対確率測度
     */
    public function relativeWhere(callable $condition): ProbabilityMeasure
    {
        return new ProbabilityMeasure(array_filter($this->measurableMap, $condition, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * 確率測度に従ったサンプリングを行う
     * @param Ratio $randomValue 0以上1以下の実数
     * @return T サンプリングされた標本点
     */
    public function samplingFrom(Ratio $randomValue) //: T
    {
        $indexes = range(0, count($this->measurableMap) - 1);
        $intervalPoints = array_map(
            fn ($index) => $this->probabilityWhere(
                /** @var T $samplePoint */
                fn (Ratio $ratio, $samplePoint) => array_key_exists($samplePoint, array_slice($this->measurableMap, 0, $index + 1))
            ),
            $indexes
        );

        // 二分探索でRandomValueがどの区間に入るかを調べる
        while (count($indexes) > 1) {
            $middleIndex = intdiv(count($indexes), 2);
            if ($randomValue->isEqualTo($intervalPoints[$middleIndex])) {
                return array_keys($this->measurableMap)[$middleIndex];
            }
            if ($randomValue->isLessThan($intervalPoints[$middleIndex])) {
                $indexes = array_slice($indexes, 0, $middleIndex);
                continue;
            }
            $indexes = array_slice($indexes, $middleIndex);
        }

        $searchedIndex = $indexes[0]; // 二分探索で見つけた区間の端点のインデックス
        if ($randomValue->isLessThanOrEqualTo($intervalPoints[$searchedIndex])) {
            return array_keys($this->measurableMap)[$searchedIndex];
        }
        return array_keys($this->measurableMap)[$searchedIndex + 1];
    }
}
