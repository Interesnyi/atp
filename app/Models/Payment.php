<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = [
        'payment_date',
        'buyer_id',
        'legal_entity_id',
        'amount',
        'comment',
        'created_at',
        'updated_at',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function legalEntity()
    {
        return $this->belongsTo(LegalEntity::class, 'legal_entity_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'payment_invoices', 'payment_id', 'invoice_id');
    }

    public function getAll($filters = [])
    {
        $sql = "SELECT p.*, b.name as buyer_name, l.name as legal_entity_name FROM {$this->table} p " .
               "LEFT JOIN buyers b ON p.buyer_id = b.id " .
               "LEFT JOIN legal_entities l ON p.legal_entity_id = l.id WHERE 1=1";
        $params = [];
        if (!empty($filters['buyer_id'])) {
            $sql .= " AND p.buyer_id = ?";
            $params[] = $filters['buyer_id'];
        }
        if (!empty($filters['legal_entity_id'])) {
            $sql .= " AND p.legal_entity_id = ?";
            $params[] = $filters['legal_entity_id'];
        }
        $sql .= " ORDER BY p.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        $sql = "SELECT p.*, b.name as buyer_name, l.name as legal_entity_name FROM {$this->table} p " .
               "LEFT JOIN buyers b ON p.buyer_id = b.id " .
               "LEFT JOIN legal_entities l ON p.legal_entity_id = l.id WHERE p.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
} 