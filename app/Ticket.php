<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'id',
        'tReference_no',
        'cat_id',
        'sub_cat_id',
        'initiator_id',
        'recommender_id',
        'tStatus',
        'tSubject',
        'tDescription',
        'is_viewed'
    ];

    /**
     * Get all of the history for the particular ticket.
     */
    public function ticket_histories()
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id');
    }

    /**
     * Get the initiator of the specific tickets.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id', 'id');
    }

    /**
     * Get the recommender of the specific tickets.
     */
    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id', 'id');
    }

    /**
     * Get the category of the specific tickets.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }

    /**
     * Get the subcategory of the specific tickets.
     */
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_cat_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'initiator_id', 'id');
    }

    public function ticketEditHistories()
    {
        return $this->hasMany(TicketEditHistory::class, 'ticket_id', 'id')->orderBy('id', 'desc');
    }
}
