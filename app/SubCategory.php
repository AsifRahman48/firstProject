<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SubCategory extends Model
{
    protected $table = 'sub_categorys';
    protected $fillable = [
        'cat_id',
        'name'
    ];

    /**
     * Get the category that owns the subcategory.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }

    /**
     * Get all of the tickets for the particular sub-category.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'sub_cat_id', 'id');
    }
}
