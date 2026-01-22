<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramLink extends AbstractModel
{
    protected $title = 'programLink';

    protected $fillable = [
        'title',
        'link_label',
        'link',
        'program_id',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
