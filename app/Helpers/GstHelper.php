<?php

if (!function_exists('normalizeGstTaxComponents')) {
    function normalizeGstTaxComponents($taxes): array
    {
        $igst = $cgst = $sgst = $cess = 0.0;

        if (empty($taxes)) {
            return compact('igst', 'cgst', 'sgst', 'cess');
        }

        if (is_string($taxes)) {
            $taxes = json_decode($taxes, true);
        }

        if (!is_array($taxes)) {
            return compact('igst', 'cgst', 'sgst', 'cess');
        }

        foreach ($taxes as $tax) {
            if (!is_array($tax)) {
                continue;
            }

            $name = strtoupper(trim($tax['tax_name'] ?? $tax['name'] ?? ''));
            $amount = (float) ($tax['tax_amount'] ?? $tax['amount'] ?? 0);

            if ($amount <= 0) {
                continue;
            }

            if ($name === 'IGST') {
                $igst += $amount;
            } elseif ($name === 'CGST') {
                $cgst += $amount;
            } elseif ($name === 'SGST') {
                $sgst += $amount;
            } elseif ($name === 'CESS') {
                $cess += $amount;
            } elseif ($name === '' || preg_match('/(^|[^A-Z])GST([^A-Z]|$)/', $name)) {
                $cgst += $amount / 2.0;
                $sgst += $amount / 2.0;
            }
        }

        return compact('igst', 'cgst', 'sgst', 'cess');
    }
}

if (!function_exists('parseGstJson')) {
    function parseGstJson($json)
    {
        $components = normalizeGstTaxComponents($json);

        return [
            'igst' => $components['igst'],
            'cgst' => $components['cgst'],
            'sgst' => $components['sgst'],
        ];
    }
}
