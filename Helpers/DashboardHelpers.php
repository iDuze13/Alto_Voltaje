<?php

/**
 * Dashboard Helper Functions
 */

if (!function_exists('formatLargeNumber')) {
    /**
     * Format large numbers with appropriate suffixes (K, M, B)
     * @param float $number
     * @param int $precision
     * @return string
     */
    function formatLargeNumber($number, $precision = 1) {
        if ($number < 1000) {
            return number_format($number, 0);
        } elseif ($number < 1000000) {
            return number_format($number / 1000, $precision) . 'K';
        } elseif ($number < 1000000000) {
            return number_format($number / 1000000, $precision) . 'M';
        } else {
            return number_format($number / 1000000000, $precision) . 'B';
        }
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency values with proper suffixes
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function formatCurrency($amount, $currency = '$') {
        if ($amount < 1000) {
            return $currency . number_format($amount, 2);
        } elseif ($amount < 1000000) {
            return $currency . number_format($amount / 1000, 1) . 'K';
        } elseif ($amount < 1000000000) {
            return $currency . number_format($amount / 1000000, 1) . 'M';
        } else {
            return $currency . number_format($amount / 1000000000, 1) . 'B';
        }
    }
}

if (!function_exists('getStatusColor')) {
    /**
     * Get CSS class for order status
     * @param string $status
     * @return string
     */
    function getStatusColor($status) {
        $statusMap = [
            'PENDIENTE' => 'pendiente',
            'CONFIRMADO' => 'confirmado',
            'EN_ESPERA' => 'en_espera',
            'ENVIADO' => 'enviado',
            'ENTREGADO' => 'entregado',
            'CANCELADO' => 'cancelado'
        ];
        
        return $statusMap[$status] ?? 'pendiente';
    }
}

if (!function_exists('getStatusText')) {
    /**
     * Get readable text for order status
     * @param string $status
     * @return string
     */
    function getStatusText($status) {
        $statusMap = [
            'PENDIENTE' => 'Pendiente',
            'CONFIRMADO' => 'Confirmado',
            'EN_ESPERA' => 'En Espera',
            'ENVIADO' => 'Enviado',
            'ENTREGADO' => 'Entregado',
            'CANCELADO' => 'Cancelado'
        ];
        
        return $statusMap[$status] ?? ucfirst(strtolower($status));
    }
}

?>