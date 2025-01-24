<?php

use App\Models\LeaveTypeList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;

function loader()
{
    echo '
        <div class="d-flex justify-content-center align-items-center">
            <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
        </div>
    ';
}
function spinner()
{
    echo '
        <div class="d-flex justify-content-center align-items-center">
            <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
        </div>
    ';
}
function url_safe_encode($m)
{
    return rtrim(strtr(base64_encode($m), '+/', '-_'), '=');
}
function url_safe_decode($m)
{
    return base64_decode(strtr($m, '-_', '+/'));
}
function show($stuff)
{
    echo '<pre>';
    print_r($stuff);
    echo '</pre>';
}
function setActive($path, $active = 'active')
{
    return call_user_func_array('Request::is', (array) $path) ? $active : '';
}
function greeting()
{
    $hour = Carbon::now()->hour;

    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour >= 12 && $hour < 18) {
        $greeting = 'Good Afternoon';
    } elseif ($hour >= 18 && $hour < 22) {
        $greeting = 'Good Evening';
    } elseif ($hour >= 22 || $hour < 5) {
        $greeting = 'Good night';
    } else {
        $greeting = 'Hello';
    }
    return $greeting;
}
function getJoinDuration($createdDate)
{
    $currentDate = new DateTime();
    $creationDate = new DateTime($createdDate);

    $interval = $currentDate->diff($creationDate);

    if ($interval->y > 0) {
        $formattedDate = $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ago";
    } elseif ($interval->m > 0) {
        $formattedDate = $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " ago";
    } elseif ($interval->d > 0) {
        $formattedDate = $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
    } elseif ($interval->h > 0) {
        $formattedDate = $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
    } else {
        $formattedDate = "Just now";
    }

    return $formattedDate;
}
function createAvatarImageFromName($name)
{
    $background = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    $image = Image::canvas(100, 100, $background);
    $initial = strtoupper(substr($name, 0, 1));
    $image->text($initial, 50, 50, function ($font) {
        $fontPath = base_path('public_html/fonts/Lobster-Regular.ttf');
        $font->size(80);
        $font->color('#ffffff');
        $font->align('center');
        $font->valign('middle');
    });
    $base64Image = $image->encode('data-url');
    return $base64Image;
}
function buildCategoryOptions($categories, $prefix = '')
{
    $options = '';
    foreach ($categories as $category) {
        $options .= "<option value=\"$category->id\">$prefix $category->name</option>";
        if ($category->hasChildren()) {
            $options .= buildCategoryOptions($category->children, $prefix . '--');
        }
    }
    return $options;
}
function get_snippet($str, $wordCount = 5, $ellipsis = '...')
{
    $words = preg_split('/([\s,\.;\?\!]+)/', strip_tags($str), $wordCount * 2 + 1, PREG_SPLIT_DELIM_CAPTURE);
    $output = '';
    $tagStack = [];
    foreach ($words as $word) {
        if (preg_match('/^<\w+/', $word, $matches)) {
            $tagStack[] = $matches[0];
        }
        $output .= $word;
        while (count($tagStack) > 0 && preg_match('/^<\/\w+>/', $output)) {
            $output .= array_pop($tagStack);
        }
        if (count($tagStack) === 0 && str_word_count($output) >= $wordCount) {
            break;
        }
    }
    if (str_word_count($output) < str_word_count(strip_tags($str))) {
        $output .= $ellipsis;
    }
    return $output;
}
function formatBytes($bytes, $decimals = 2) {
    $sizeUnits = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sizeUnits[$factor];
}

function formatTimeLimit($minutes) {
    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    $formatted = '';
    if ($hours > 0) {
        $formatted .= $hours . ' hr' . ($hours > 1 ? 's' : '');
    }
    if ($remainingMinutes > 0) {
        if (!empty($formatted)) {
            $formatted .= ' ';
        }
        $formatted .= $remainingMinutes . ' min' . ($remainingMinutes > 1 ? 's' : '');
    }

    return $formatted;
}

function dateDifference($startDate, $endDate) {
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    $duration = $start->diff($end);

    $formattedDuration = [];

    if ($duration->y > 0) {
        $formattedDuration[] = $duration->y . " year" . ($duration->y > 1 ? "s" : "");
    }
    if ($duration->m > 0) {
        $formattedDuration[] = $duration->m . " month" . ($duration->m > 1 ? "s" : "");
    }
    if ($duration->d > 0) {
        $formattedDuration[] = $duration->d . " day" . ($duration->d > 1 ? "s" : "");
    }
    if ($duration->h > 0) {
        $formattedDuration[] = $duration->h . " hour" . ($duration->h > 1 ? "s" : "");
    }
    if ($duration->i > 0) {
        $formattedDuration[] = $duration->i . " minute" . ($duration->i > 1 ? "s" : "");
    }
    if ($duration->s > 0) {
        $formattedDuration[] = $duration->s . " second" . ($duration->s > 1 ? "s" : "");
    }

    return !empty($formattedDuration) ? implode(", ", $formattedDuration) : "0 seconds";
}
function formatStatus($status)
{
    return ucwords(str_replace('_', ' ', $status));
}
function userHasCreatorPrivileges(): bool
{
    $user = Auth::user();
    return $user ? $user->hasCreatorPrivileges() : false;
}
function formatDuration($seconds) {
    return gmdate(($seconds >= 3600 ? 'H:i:s' : 'i:s'), $seconds);
}

function getLeaveTypeNames()
    {
        $leaveTypes = LeaveTypeList::all();
        Log::debug($leaveTypes);
        return $leaveTypes->pluck('name')->toArray();
    }
