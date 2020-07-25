<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    //
    protected $table = "items";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'id', 'to_bank_label', 'score', 'created_by', 'modified_by', 'created_at', 'modified_at', 'is_deleted'
    ];

}
