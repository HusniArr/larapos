<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kd_brg';
    protected $keyType = 'string';
    protected $incrementing = false;
    protected $timestamps = false;

    protected $fillable = ['kd_brg', 'nm_brg', 'hrg_beli', 'hrg_jual', 'jml_brg', 'satuan'];
}
