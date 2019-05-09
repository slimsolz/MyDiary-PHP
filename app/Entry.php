<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['title', 'category', 'image', 'story', 'userId'];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
