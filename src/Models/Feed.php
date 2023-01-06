<?php

namespace Eftersom\Larafeed\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Feed extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id   = (string) Str::uuid();
            $timestamp   = Carbon::now()->timestamp;
            $model->slug = Str::slug($model->title . '-' . $timestamp);
        });
    }

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'feeds';

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'link',
        'description',
        'language',
        'image',
    ];

    /**
     * @return BelongsToMany
     */
    public function feeds()
    {
        return $this->belongsToMany('App\Models\User')->withPivot(['user_id', 'feed_id']);
    }
}
