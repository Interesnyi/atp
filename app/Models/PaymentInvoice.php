<?php

namespace App\Models;

use App\Core\Model;

class PaymentInvoice extends Model
{
    protected $table = 'payment_invoices';
    protected $fillable = [
        'payment_id',
        'invoice_id',
    ];

    public function deleteByPaymentId($paymentId) {
        $sql = "DELETE FROM {$this->table} WHERE payment_id = ?";
        return $this->db->query($sql, [$paymentId]);
    }
} 