<?php

namespace App\Common\Math;

use DomainException;

/**
 * 比率を表すクラス
 */
class Ratio
{
    public function __construct(
        private readonly int $nominal,
        private readonly int $denominator
    ) {
        if ($nominal < 0 || $denominator <= 0) {
            throw new DomainException('Ratio value must be greater than zero.');
        }
    }

    /**
     * 非負整数を比率に変換する
     * @param int $value
     * @return Ratio
     */
    public static function createfromInt(int $value): Ratio
    {
        return new Ratio($value, 1);
    }

    public static function ZERO(): Ratio
    {
        return new Ratio(0, 1);
    }

    public static function ONE(): Ratio
    {
        return new Ratio(1, 1);
    }

    /**
     * 比率の等価性
     * @param Ratio $other
     * @return bool
     */
    public function isEqualTo(Ratio $other): bool
    {
        return $this->nominal * $other->denominator === $this->denominator * $other->nominal;
    }

    /**
     * 比率同士の加算
     * @param Ratio $other
     * @return Ratio
     */
    public function pluss(Ratio $other): Ratio
    {
        return new Ratio(
            $this->nominal * $other->denominator + $this->denominator * $other->nominal,
            $this->denominator * $other->denominator
        );
    }

    /**
     * 比率同士の割り算
     * @param Ratio $other
     * @return Ratio
     */
    public function dividedBy(Ratio $other): Ratio
    {
        return new Ratio(
            $this->nominal * $other->denominator,
            $this->denominator * $other->nominal
        );
    }

    /**
     * 比率の大なり比較
     * @param Ratio $other
     * @return bool
     */
    public function isGreaterThan(Ratio $other): bool
    {
        return $this->nominal * $other->denominator > $this->denominator * $other->nominal;
    }

    /**
     * 比率の大なりイコール比較
     * @param Ratio $other
     * @return bool
     */
    public function isGreaterThanOrEqualTo(Ratio $other): bool
    {
        return $this->isGreaterThan($other) || $this->isEqualTo($other);
    }

    /**
     * 比率の小なり比較
     * @param Ratio $other
     * @return bool
     */
    public function isLessThan(Ratio $other): bool
    {
        return $this->nominal * $other->denominator < $this->denominator * $other->nominal;
    }

    /**
     * 比率の小なりイコール比較
     * @param Ratio $other
     * @return bool
     */
    public function isLessThanOrEqualTo(Ratio $other): bool
    {
        return $this->isLessThan($other) || $this->isEqualTo($other);
    }

    /**
     * 比率の近似値を計算する.
     * 丸誤差が発生する可能性があるため、厳密に等価・比較・演算をする際は別メソッドを使用すること
     * @return float
     */
    public function calcApproximation(): float
    {
        return $this->nominal / $this->denominator;
    }

    /**
     * 比率の配列から合計値を計算する
     * @param Ratio[] $ratioArray
     * @return Ratio
     */
    public static function calcSum(array $ratioArray): Ratio
    {
        return array_reduce($ratioArray, function (Ratio $carry, Ratio $ratio) {
            return $carry->pluss($ratio);
        }, new Ratio(0, 1));
    }
}
