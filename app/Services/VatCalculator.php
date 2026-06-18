<?php

namespace App\Services;

class VatCalculator
{
    public function parseAmount($text)
    {
        return (!empty($text) && strlen($text) > 0) ? floatval($text) : 0;
    }

    public function calculateAmountReceivable($params)
    {
        $amount = $params['amount'] ?? 0;
        $utilitiesAmount = $params['utilitiesAmount'] ?? 0;
        return $amount - $utilitiesAmount;
    }

    public function calculateVatUnit($vatValue)
    {
        return (100 + $vatValue) / 100;
    }

    public function calculateVatAmount($params)
    {
        $amount = $this->parseAmount($params['amountText'] ?? '');
        $vatPercentage = $params['vat'] ?? 0;
        return $amount * ($vatPercentage / (100 + $vatPercentage));
    }

    public function calculateCostOfUnit($params)
    {
        $amount = $this->parseAmount($params['amountText'] ?? '');
        $vatAmount = $this->calculateVatAmount($params);
        return $amount - $vatAmount;
    }

    public function calculateTariffAmountPerKWatt($params)
    {
        $costOfUnit = $this->calculateCostOfUnit($params);
        $rate = $params['tariffAmount'] ?? 0;
        dd($costOfUnit, $rate, $params);
        return $rate > 0 ? $costOfUnit / $rate : 0;
    }
}
