<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Publication;
use App\Models\DownloadHistory;
use App\Models\Attachment;
use App\Enums\PublicationTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\NewsletterSubscription;
use Exception;

class PublicationController extends Controller
{
    public function publications(Request $request)
    {
        $banner = Banner::getBannerFor('Publication');

        return view('publication.all-publications', compact('banner'));
    }

    public function auditReports(Request $request)
    {
        $banner = Banner::getBannerFor('Audit Report');

        $publicationsQuery = Publication::active()
            ->where('publication_type', PublicationTypeEnum::AUDIT_REPORT);

        $this->applyDateFilters($publicationsQuery, $request);

        if ($request->ajax()) {
            return $this->generateAjaxResponse($publicationsQuery, true);
        } else {
            $publications = $publicationsQuery->orderBy('published_date', 'desc')->paginate(12);
            return view('publication.publication', compact('publications','banner'));
        }
    }

    public function books(Request $request)
    {
        $banner = Banner::getBannerFor('Book');

        $booksQuery = Publication::active()
            ->where('publication_type', PublicationTypeEnum::BOOK);

        $this->applyDateFilters($booksQuery, $request);

        if ($request->ajax()) {
            return $this->generateAjaxResponse($booksQuery, true);
        } else {
            $books = $booksQuery->orderBy('published_date', 'desc')->paginate(12);
            return view('publication.book', compact('books','banner'));
        }
    }

    public function reports(Request $request)
    {
        $banner = Banner::getBannerFor('Report');

        $reportsQuery = Publication::active()
            ->where('publication_type', PublicationTypeEnum::REPORT);

        $this->applyDateFilters($reportsQuery, $request);

        if ($request->ajax()) {
            return $this->generateAjaxResponse($reportsQuery);
        } else {
            $reports = $reportsQuery->orderBy('published_date', 'desc')->paginate(12);
            return view('publication.report', compact('reports','banner'));
        }
    }

    public function newsletters(Request $request)
    {
        $banner = Banner::getBannerFor('Newsletter');

        $newslettersQuery = Publication::active()
            ->where('publication_type', PublicationTypeEnum::NEWSLETTER);

        $this->applyDateFilters($newslettersQuery, $request);

        if ($request->ajax()) {
            return $this->generateAjaxResponse($newslettersQuery);
        } else {
            $newsletters = $newslettersQuery->orderBy('published_date', 'desc')->paginate(12);
            return view('publication.newsletter', compact('newsletters','banner'));
        }
    }

    private function generateAjaxResponse($query, $thumbnail = false)
    {
        $items = $query->orderBy('published_date', 'desc')->paginate(12);

        $htmlView = view('publication.filtered_items', [
            'items' => $items,
            'thumbnail' => $thumbnail,
        ])->render();

        return response()->json($htmlView);
    }

    private function applyDateFilters($query, $request)
    {
        if ($request->filled('month')) {
            $query->whereMonth('published_date', $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->whereYear('published_date', $request->input('year'));
        }
    }

    public function download(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $attachment = $publication->attachment;

        if ($request->isMethod('post') && !$request->user()) {
            $validated = $request->validate([
                'email' => 'required|email',
                'name' => 'required|string',
                'mobile_no' => 'nullable|numeric',
            ]);

            $response = $this->downloadFile($attachment, $publication, $request);

            if ($response && $response->getStatusCode() === 200) {
                $this->saveInformation($request, $publication);
            }

            return $response;
        } elseif ($request->user()) {
            $response = $this->downloadFile($attachment, $publication, $request);

            if ($response->getStatusCode() === 200) {
                $this->saveInformation($request, $publication);
            }

            return $response;
        } else {
            return view('components.download_modal', ['downloadPath' => route('download', ['id' => $id])]);
        }
    }

    private function downloadFile($attachment)
    {
        try {
            $filePath = Storage::disk('admin')->path($attachment->file);
            if (File::exists($filePath)) {
                return response()->download($filePath);
            } else {
                throw new Exception("File not found at path: {$filePath}");
            }
        } catch (Exception $exception) {
            return $this->handleDownloadError($exception);
        }
    }

    private function saveInformation(Request $request, Publication $publication)
    {
        $attachment = $publication->attachment;
        $attachment->increment('download_count');

        DownloadHistory::create([
            'email' => $request->user() ? $request->user()->email : $request->input('email'),
            'name' => $request->user() ? $request->user()->getFullNameAttribute() : $request->input('name'),
            'mobile_no' => $request->user() ? $request->user()->mobile_no : $request->input('mobile_no'),
            'publication_id' => $publication->id,
            'registered_user' => $request->user() ? true : false,
            'newsletter_subscribed' => $this->userSubscribedToNewsletter($request),
        ]);
    }

    private function handleDownloadError(Exception $exception)
    {
        return redirect()->back()->with('error', 'Error occurred while trying to download the file. Please check the logs for more information.');
    }

    private function userSubscribedToNewsletter($request)
    {
        $user_email = $request->user() ? $request->user()->email : $request->input('email');
        return NewsletterSubscription::where('email', $user_email)->exists();
    }
}
