<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Kategori extends Model
{
    protected $guarded = ['id'];

    public function buku()
    {
        return  $this->hasMany(Buku::class);
    }
    //
}
