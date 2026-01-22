<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Validation\ValidationException;
class JobApplication extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['job_post_id', 'applicant_name', 'mobile_no', 'email', 'comment','cv'];

    /**
     * The job post that the application belongs to.
     */
    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    protected static $rules = [
        'applicant_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'mobile_no' => 'required',
        'cv' => 'required|file'
    ];

    public function validate()
    {
        $validator = Validator::make($this->attributesToArray(), static::$rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
