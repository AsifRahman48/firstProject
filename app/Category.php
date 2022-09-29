<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $table = 'categorys';
    protected $fillable = [
        'name'
    ];

    /**
     * Get all of the subcategory for the category.
     */
    public function sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'cat_id', 'id');
    }

    /**
     * Get all of the tickets for the particular category.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'cat_id', 'id');
    }

    public function user_category()
    {
        return $this->hasMany(UserCategory::class, 'category_id', 'id');
    }
}
