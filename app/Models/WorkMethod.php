<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkMethod extends Model
{
    protected $table = "work_methods";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "name",
        "label",
        "description",
        "external_url",
    ];

    //
    // Relationships
    //

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}