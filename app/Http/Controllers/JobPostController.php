<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\Banner;
use App\Models\JobApplication;
use App\Models\JobPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class JobPostController extends Controller
{
    public function index()
    {

        $banner = Banner::getBannerFor('Career');

        $currentDate = Carbon::today();

        $jobPosts = JobPost::where('closing_date', '>=', $currentDate)
            ->orderBy('closing_date')
            ->get();

        return view('job_post.index')->with(['jobPosts' => $jobPosts,'banner' => $banner]);
    }

    public function show($slug): View|RedirectResponse
    {
        $jobPost = JobPost::findBySlug($slug);

        if ($jobPost && Carbon::parse($jobPost->closing_date)->endOfDay()->gte(Carbon::today())) {
            return view('job_post.show')
                ->with('jobPost', $jobPost);
        }

        FlashHelper::trigger('JobPost not found or application period has ended!', 'danger');
        return redirect()->route('programs');
    }

    public function filterJobs(Request $request)
    {
        $jobPostsQuery = JobPost::query();

        $title = $request->input('title');
        $deadline = $request->input('deadline');

        if (!empty($title)) {
            $jobPostsQuery->where('title', 'LIKE', "%{$title}%")->orderBy('closing_date')
                ->get();;
        }

        if (!empty($deadline)) {
            $jobPostsQuery->where('closing_date', '>=', $deadline)->orderBy('closing_date')
                ->get();
        }


        $jobPosts = $jobPostsQuery->get();

        if(empty($title) && empty($deadline)){
            $currentDate = Carbon::today();
            $jobPosts = JobPost::where('closing_date', '>=', $currentDate)->get();
        }

        return view('job_post.filtered_jobs', compact('jobPosts'));
    }


    public function submit(Request $request)
    {
        $validatedData = $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
            'applicant_name' => 'required|max:255',
            'mobile_no' => 'required|numeric',
            'email' => 'required|email|max:255',
            'comment' => 'nullable',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:51200',
        ]);



        if ($request->hasFile('cv') && $request->file('cv')->isValid()) {
            $validatedData['cv'] = $request->cv->store('cvs', 'public');
        }

        try {
            $jobApplication = new JobApplication($validatedData);

            if ($jobApplication->save()) {
                $message = ['message' => 'Successfully submitted.', 'status' => 'success'];
            }


            if ($request->ajax()) {
                return response()->json($message);
            } else {
                return redirect()->back()->with('message', $message['message'])->with('status', $message['status']);
            }
        } catch (\Exception $e) {
            $message = ['message' => 'There was a problem submitting your form.', 'status' => 'fail'];


            if ($request->ajax()) {
                return response()->json($message);
            } else {
                return redirect()->back()->with('error', $message['message']);
            }
        }
    }
}
