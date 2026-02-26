<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstateService extends Model
{
    use HasFactory;


    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    protected $casts = [
        'status' => 'integer'
    ];

    public function updateRating()
    {
        $average = DB::table('comments')
            ->where('job_id', $this->id)
            ->avg('rate');

        $this->rating = round($average ?? 0, 1);
        $this->save();
    }



}
