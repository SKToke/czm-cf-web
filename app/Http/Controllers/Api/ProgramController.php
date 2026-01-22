<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    use HttpResponses;

    public function allPrograms(): JsonResponse
    {
        $allPrograms = Program::all();
        $data = [];

        if(empty($allPrograms)) return $this->success('No program available');

        foreach ($allPrograms as $program) $data []= $this->getProgramBaseInfo($program);

        return $this->success('List of All Programs (id, slug, title and logo)', $data);
    }

    public function defaultPrograms(): JsonResponse
    {
        $defaultPrograms = Program::getDefaults();
        $data = [];

        if(empty($defaultPrograms)) return $this->success('No program available');

        foreach ($defaultPrograms as $program) $data []= $this->getProgramBaseInfo($program);

        return $this->success('List of Default Programs (id, slug, title and logo)', $data);
    }

    public function details($slug): JsonResponse
    {
        $program = Program::findBySlug($slug);
        if(is_null($program)) return $this->error('Program not found');

        $program->logo = $program->getLogo();
        if($program->hasPhotos()) $program->photos = $program->getPhotos();

        $program->program_web_link = route('program-details', ['slug' => $program->slug]);

        return $this->success('Program details', $program);
    }

    public function getProgramBaseInfo(Program $program): array
    {
        return [
            'id' => $program->id,
            'slug' => $program->slug,
            'title' => $program->title,
            'logo' => $program->getLogo(),
            ];
    }
}
