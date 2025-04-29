<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\LeadActivity;
use App\Models\Business;
use App\Models\ContactSubmission;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LeadsExport;
use App\Exports\ContactsExport;
use App\Exports\CampaignsExport;
use App\Exports\SurveysExport;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\ShortLink;
use App\Models\ShortLinkVisit;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\SurveyConfirmation;

use Illuminate\Support\Str;

class CrmController extends Controller
{
    use HandleTransactions;

    // Contact Submissions
    public function contacts()
    {
        return view('crm.contacts.index', ['page' => 'Contact Submissions']);
    }

    public function viewContact($business, ContactSubmission $submission)
    {
        return view('crm.contacts.show', compact('submission'));
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'inquiry_type' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|in:new,contacted,qualified,closed',
            'utm_source' => 'nullable|string',
            'utm_medium' => 'nullable|string',
            'utm_campaign' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $submission = ContactSubmission::create([
                'business_id' => $validated['business_id'],
                'first_name' => $validated['first_name'] ?? '',
                'last_name' => $validated['last_name'] ?? '',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'country' => $validated['country'] ?? null,
                'inquiry_type' => $validated['inquiry_type'],
                'message' => $validated['message'],
                'source' => 'manual',
                'utm_source' => $validated['utm_source'] ?? null,
                'utm_medium' => $validated['utm_medium'] ?? null,
                'utm_campaign' => $validated['utm_campaign'] ?? null,
                'status' => $validated['status'],
            ]);

            Lead::create([
                'contact_submission_id' => $submission->id,
                'name' => trim($submission->first_name . ' ' . $submission->last_name) ?: 'Unknown',
                'email' => $submission->email,
                'phone' => $submission->phone,
                'source' => 'manual',
                'status' => $submission->status,
                'user_id' => auth()->id(),
            ]);

            return RequestResponse::created('Contact created successfully.', ['submission' => $submission]);
        });
    }

    public function fetchContacts(Request $request)
    {
        $businessSlug = session('active_business_slug');
        $query = ContactSubmission::query()->orderBy('created_at', 'desc');

        if ($businessSlug) {
            $business = \App\Models\Business::findBySlug($businessSlug);
            if (!$business) {
                Log::warning('Invalid business slug', ['slug' => $businessSlug]);
                return response()->json(['error' => 'Invalid business slug'], 404);
            }
            $query->where('business_id', $business->id);
        } else {
            Log::warning('No active business slug in session');
            return response()->json(['error' => 'No active business selected'], 400);
        }

        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', "%$filter%")
                    ->orWhere('last_name', 'like', "%$filter%")
                    ->orWhere('email', 'like', "%$filter%")
                    ->orWhere('message', 'like', "%$filter%")
                    ->orWhere('company_name', 'like', "%$filter%")
                    ->orWhere('inquiry_type', 'like', "%$filter%");
            });
        }

        $submissions = $query->get();
        $table = view('crm.contacts._table', compact('submissions'))->render();
        Log::info('Rendered table HTML', ['table' => $table]);
        return response()->json(['data' => $table]);
    }

    public function createContact()
    {
        return view('crm.contacts.create', ['page' => 'Submit Contact']);
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:contact_submissions,id',
            'status' => 'required|in:new,contacted,qualified,closed',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $submission = ContactSubmission::findOrFail($validated['id']);
            $submission->update(['status' => $validated['status']]);
            return RequestResponse::ok('Contact updated successfully.', ['submission' => $submission]);
        });
    }

    public function destroyContact(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:contact_submissions,id',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $submission = ContactSubmission::findOrFail($validated['id']);
            $submission->delete();
            return RequestResponse::ok('Contact deleted successfully.');
        });
    }

    // Campaigns
    public function campaigns()
    {
        return view('crm.campaigns.index', ['page' => 'Campaigns']);
    }

    public function createCampaign()
    {
        return view('crm.campaigns.create', ['page' => 'Create Campaign']);
    }

    public function viewCampaign($business, Campaign $campaign)
    {
        return view('crm.campaigns.show', compact('campaign'));
    }

    public function fetchCampaigns(Request $request)
    {
        $businessSlug = session('active_business_slug');
        $query = Campaign::query()->orderBy('created_at', 'desc');

        if ($businessSlug) {
            $business = \App\Models\Business::findBySlug($businessSlug);
            $query->where('business_id', $business->id);
        }

        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where(fn($q) => $q->where('name', 'like', "%$filter%")
                ->orWhere('utm_campaign', 'like', "%$filter%"));
        }

        $campaigns = $query->get();
        $table = view('crm.campaigns._table', compact('campaigns'))->render();
        return RequestResponse::ok('Ok', $table);
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'utm_source' => 'required|string|max:255',
            'utm_medium' => 'required|string|max:255',
            'utm_campaign' => 'required|string|max:255',
            'target_url' => 'required|url|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,completed',
            'has_survey' => 'boolean',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $business = \App\Models\Business::findBySlug(session('active_business_slug'));
            $campaign = $business->campaigns()->create($validated);

            // Generate short link with slug
            $baseSlug = Str::slug($campaign->name);
            $slug = $baseSlug;
            $counter = 1;
            while (ShortLink::where('slug', $slug)->exists()) {
                $slug = "$baseSlug-$counter";
                $counter++;
            }

            $shortLink = ShortLink::create([
                'campaign_id' => $campaign->id,
                'slug' => $slug,
                'visits' => 0,
            ]);

            return RequestResponse::created('Campaign created successfully.', ['campaign' => $campaign, 'short_link' => $shortLink]);
        });
    }

    public function destroyCampaign(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:campaigns,id',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $campaign = Campaign::findOrFail($validated['id']);
            $campaign->delete();
            return RequestResponse::ok('Campaign deleted successfully.');
        });
    }

    // handle short link
    public function handleShortLink($slug)
    {
        $shortLink = ShortLink::where('slug', $slug)->firstOrFail();
        $campaign = $shortLink->campaign;

        $ip = request()->ip();
        $userAgent = request()->userAgent();

        \Log::debug("Processing short link: {$slug}, IP: {$ip}");

        // Check if a visit from this IP already exists for this short link
        $existingVisit = ShortLinkVisit::where('short_link_id', $shortLink->id)
            ->where('ip_address', $ip)
            ->first();

        // Only record if not already visited by this IP
        if (!$existingVisit) {
            $country = null;

            // Skip geolocation for localhost/bogon IPs during local testing
            if ($ip === '127.0.0.1' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                \Log::debug("Skipping geolocation for private/bogon IP: {$ip}");
                $country = 'Development';
            } else {
                try {
                    $response = Http::timeout(5)->get("https://ipinfo.io/{$ip}/json?token=a876c4d470b426");
                    \Log::debug('ipinfo.io Response: ' . json_encode($response->json()));

                    if ($response->successful()) {
                        if (isset($response['bogon']) && $response['bogon'] === true) {
                            \Log::debug("Bogon IP detected: {$ip}");
                            $country = 'Unknown';
                        } else {
                            $country = $response['country'] ?? null;
                        }
                    } else {
                        \Log::warning("ipinfo.io request failed for IP {$ip}: " . json_encode($response->json()));
                    }
                } catch (\Exception $e) {
                    \Log::warning("Failed to fetch country for IP {$ip}: {$e->getMessage()}");
                }
            }

            $agent = new Agent();

            ShortLinkVisit::create([
                'short_link_id' => $shortLink->id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'browser' => $agent->browser(),
                'os' => $agent->platform(),
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'country' => $country,
            ]);

            $shortLink->increment('visits');
        }

        if ($campaign->has_survey) {
            return view('crm.surveys.form', compact('campaign', 'shortLink'));
        }

        return redirect()->away($campaign->target_url);
    }

    public function skipShortLink($slug)
    {
        $shortLink = ShortLink::where('slug', $slug)->firstOrFail();
        $ip = request()->ip();

        // Update visit to mark as skipped
        ShortLinkVisit::where('short_link_id', $shortLink->id)
            ->where('ip_address', $ip)
            ->update(['skipped' => true]);

        return redirect()->away($shortLink->campaign->target_url);
    }

    public function submitSurvey(Request $request, $slug)
    {
        $shortLink = ShortLink::where('slug', $slug)->firstOrFail();
        $campaign = $shortLink->campaign;

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'country' => 'required|string|max:100',
            'message' => 'required|string',
        ]);

        // Check for existing lead to prevent duplicates
        $existingLead = Lead::where('campaign_id', $campaign->id)
            ->where('email', $validated['email'])
            ->first();

        if ($existingLead) {
            return response()->json([
                'message' => 'You have already submitted feedback for this campaign.',
                'redirect_url' => $campaign->target_url,
            ], 409); // Conflict status
        }

        return $this->handleTransaction(function () use ($validated, $campaign) {
            $lead = Lead::create([
                'business_id' => $campaign->business_id,
                'campaign_id' => $campaign->id,
                'name' => $validated['name'] ?: 'Anonymous',
                'email' => $validated['email'],
                'country' => $validated['country'],
                'message' => $validated['message'],
                'status' => 'new',
            ]);

            // Log activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => null, // Public user, no auth
                'activity_type' => 'note',
                'description' => 'Survey submitted via campaign.',
            ]);

            // Send confirmation email
            try {
                Mail::to($validated['email'])->send(new SurveyConfirmation($campaign, $lead));
            } catch (\Exception $e) {
                \Log::error("Failed to send survey confirmation email: {$e->getMessage()}");
                // Continue despite email failure
            }

            return response()->json([
                'message' => 'Thank you for your feedback! A confirmation has been sent to your email.',
                'redirect_url' => $campaign->target_url,
            ]);
        });
    }

    public function exportSurveys($slug, Campaign $campaign)
    {
        $currentBusiness = Business::findBySlug(session('active_business_slug'));
        if ($campaign->business_id !== $currentBusiness->id) {
            abort(403);
        }

        $leads = $campaign->leads()->latest()->get();
        return Excel::download(new SurveysExport($leads), "survey_results_{$campaign->slug}.xlsx");
    }

    public function analytics(Request $request, $slug, Campaign $campaign)
    {
        $currentBusiness = Business::findBySlug(session('active_business_slug'));
        if ($campaign->business_id !== $currentBusiness->id) {
            abort(403);
        }

        return view('crm.campaigns.analytics', compact('currentBusiness', 'campaign'));
    }

    public function fetchAnalytics(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'type' => 'required|in:visits,survey',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);
        $business = Business::findBySlug(session('active_business_slug'));
        if ($campaign->business_id !== $business->id) {
            abort(403);
        }

        if ($request->type === 'visits') {
            $visits = $campaign->shortLink ? $campaign->shortLink->visits()->latest()->get() : collect([]);
            return response()->json([
                'data' => view('crm.campaigns.partials.visits_table', compact('visits'))->render(),
            ]);
        } else {
            $leads = $campaign->leads()->latest()->get();
            return response()->json([
                'data' => view('crm.campaigns.partials.survey_table', compact('leads'))->render(),
            ]);
        }
    }

    // Leads
    public function leads()
    {
        return view('crm.leads.index', ['page' => 'Leads']);
    }

    public function createLead()
    {
        return view('crm.leads.create', ['page' => 'Create Lead']);
    }

    public function viewLead($business, Lead $lead)
    {
        $activities = $lead->activities()->orderBy('created_at', 'desc')->get();
        return view('crm.leads.show', compact('lead', 'activities'));
    }

    public function fetchLeads(Request $request)
    {
        $businessSlug = session('active_business_slug');
        $query = Lead::query()->orderBy('created_at', 'desc');

        if ($businessSlug) {
            $business = \App\Models\Business::findBySlug($businessSlug);
            $query->whereHas('user', fn($q) => $q->whereHas('business', fn($b) => $b->where('id', $business->id)))
                ->orWhereHas('campaign', fn($q) => $q->where('business_id', $business->id));
        }

        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where(fn($q) => $q->where('name', 'like', "%$filter%")
                ->orWhere('email', 'like', "%$filter%")
                ->orWhere('label', 'like', "%$filter%"));
        }

        $leads = $query->get();
        $table = view('crm.leads._table', compact('leads'))->render();
        return RequestResponse::ok('Ok', $table);
    }

    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:new,contacted,qualified,converted,lost',
            'label' => 'nullable|string|max:255',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $lead = Lead::create(array_merge($validated, ['user_id' => auth()->id()]));
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'activity_type' => 'note',
                'description' => 'Lead created.',
            ]);
            return RequestResponse::created('Lead created successfully.', ['lead' => $lead]);
        });
    }

    public function labelLead(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'label' => 'required|string|max:255',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $lead = Lead::findOrFail($validated['lead_id']);
            $lead->update(['label' => $validated['label']]);
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'activity_type' => 'status_change',
                'description' => "Lead labeled as '{$validated['label']}'.",
            ]);
            return RequestResponse::ok('Lead labeled successfully.', ['lead' => $lead]);
        });
    }

    public function storeLeadActivity(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'activity_type' => 'required|in:call,email,meeting,note',
            'description' => 'required|string',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $activity = LeadActivity::create(array_merge($validated, ['user_id' => auth()->id()]));
            return RequestResponse::created('Activity logged successfully.', ['activity' => $activity]);
        });
    }

    // Reports
    public function reports()
    {
        return view('crm.reports.index', ['page' => 'CRM Reports']);
    }

    public function exportReport(Request $request, $type, $format)
    {
        $businessSlug = session('active_business_slug');
        $business = \App\Models\Business::findBySlug($businessSlug);

        if ($type === 'contacts') {
            $contacts = ContactSubmission::where('business_id', $business->id)->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('crm.reports.contacts_pdf', ['contacts' => $contacts]);
                return $pdf->download('contacts_report.pdf');
            } elseif ($format === 'csv' || $format === 'xlsx') {
                return Excel::download(new ContactsExport($contacts), "contacts_report.$format");
            }
        } elseif ($type === 'leads') {
            $leads = Lead::whereHas('user', fn($q) => $q->whereHas('business', fn($b) => $b->where('id', $business->id)))
                ->orWhereHas('campaign', fn($q) => $q->where('business_id', $business->id))
                ->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('crm.reports.leads_pdf', ['leads' => $leads]);
                return $pdf->download('leads_report.pdf');
            } elseif ($format === 'csv' || $format === 'xlsx') {
                return Excel::download(new LeadsExport($leads), "leads_report.$format");
            }
        } elseif ($type === 'campaigns') {
            $campaigns = Campaign::where('business_id', $business->id)->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('crm.reports.campaigns_pdf', ['campaigns' => $campaigns]);
                return $pdf->download('campaigns_report.pdf');
            } elseif ($format === 'csv' || $format === 'xlsx') {
                return Excel::download(new CampaignsExport($campaigns), "campaigns_report.$format");
            }
        }

        return redirect()->back()->with('error', 'Invalid report type or format.');
    }
}
