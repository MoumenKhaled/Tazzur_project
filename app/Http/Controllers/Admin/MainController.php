<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Job;
use App\Models\User;
use App\Models\Course;
use App\Models\Survey;
use App\Models\Advisor;
use App\Models\Company;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;

class MainController extends BaseController
{
    public function getStatistics()
    {

        $totalUsers = DB::table('users')->count();
        $usersWithJobApplications = DB::table('job_applications')->distinct('user_id')->count('user_id');
        $percentage = ($totalUsers == 0) ? 0 : ($usersWithJobApplications / $totalUsers) * 100;
        $percentage = number_format($percentage, 2);
        $topicsData = DB::table('users')
            ->select('topic')
            ->get()
            ->pluck('topic');
        $topicsCount = [];
        foreach ($topicsData as $topicsJson) {
            $topicsArray = json_decode($topicsJson, true);
            if (is_array($topicsArray)) {
                foreach ($topicsArray as $topic) {
                    if (isset($topicsCount[$topic])) {
                        $topicsCount[$topic]++;
                    } else {
                        $topicsCount[$topic] = 1;
                    }
                }
            }
        }
        arsort($topicsCount);
        $topTopics = array_slice($topicsCount, 0, 5, true);
        $totalTopics = array_sum($topicsCount);
        $topTopicsWithPercentages = [];
        foreach ($topTopics as $topic => $count) {
            $translatedTopic = trans('data.' . $topic);
            $topTopicsWithPercentages[] = [
                'topic' => $translatedTopic,
                'percent' => ($totalTopics == 0) ? 0 : number_format(($count / $totalTopics) * 100, 2)
            ];
        }


        $companiesData = DB::table('job')
            ->join('companies', 'job.company_id', '=', 'companies.id')
            ->select('companies.name')
            ->get()
            ->pluck('name')
            ->toArray();

        $companiesCount = array_count_values($companiesData);
        arsort($companiesCount);
        $topCompanies = array_slice($companiesCount, 0, 5, true);


        $totalJobs = array_sum($companiesCount);
        $topCompaniesWithPercentages = [];
        foreach ($topCompanies as $company => $count) {
            $topCompaniesWithPercentages[] = [
                'company' => $company,
                'percent' => ($totalJobs == 0) ? 0 : number_format(($count / $totalJobs) * 100, 2)
            ];
        }

        $jobsByLocation = DB::table('job')
            ->select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->get();
        $jobsByLocation = $jobsByLocation->map(function($item) {
    $item->location = trans('data.' . $item->location);
    return $item;
});
        $coursesByLocation = DB::table('courses')
            ->select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->get();
                $coursesByLocation = $coursesByLocation->map(function($item) {
    $item->location = trans('data.' . $item->location);
    return $item;
});


        $topJobTopics = DB::table('job')
            ->select('topic', DB::raw('count(*) as total'))
            ->groupBy('topic')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

                        $topJobTopics = $topJobTopics->map(function($item) {
                       $item->topic = trans('data.' . $item->topic);
    return $item;
});

        $topCourseTopics = DB::table('courses')
            ->select('topic', DB::raw('count(*) as total'))
            ->groupBy('topic')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
                                $topCourseTopics = $topCourseTopics->map(function($item) {
                                
    $item->topic = trans('data.' . $item->topic);
    return $item;
});
$jobCounts = DB::table('job')
    ->select(DB::raw('json_unquote(json_extract(job_environment, "$[*]")) as environment'))
    ->get()
    ->flatMap(function ($item) {
        return json_decode($item->environment);
    })
    ->countBy()
    ->all();
$jobTotal = array_sum($jobCounts);
$jobsOnlineOfflineRatio = collect($jobCounts)->mapWithKeys(function ($count, $environment) use ($jobTotal) {
    $translatedEnvironment = trans('data.' . $environment); // ??????? ??? ??????
    $percentage = $jobTotal > 0 ? ($count / $jobTotal) * 100 : 0;
    return [$translatedEnvironment => number_format($percentage, 2)];
});
Log::info('jobsOnlineOfflineRatio: ', ['ratio' => $jobsOnlineOfflineRatio->toArray()]);


  // Fetching and processing course data
$courseCounts = DB::table('courses')
    ->select(DB::raw('json_unquote(json_extract(type, "$[*]")) as type'))
    ->get()
    ->flatMap(function ($item) {
        // ????? ???????? ?? JSON ??? ?????? ???? ???? ??????? ?? ?????? ?????
        return json_decode($item->type);
    })
    ->countBy()
    ->all();

$courseTotal = array_sum($courseCounts);

$coursesOnlineOfflineRatio = collect($courseCounts)->mapWithKeys(function ($count, $type) use ($courseTotal) {
    $translatedType = trans('data.' . $type); // ??????? ??? ?????
    $percentage = $courseTotal > 0 ? ($count / $courseTotal) * 100 : 0;
    return [$translatedType => number_format($percentage, 2)];
});

// You might log or return this data as needed
Log::info('Courses Online/Offline Ratio: ', ['ratio' => $coursesOnlineOfflineRatio->toArray()]);



        $topAdvisors = DB::table('advisors')
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get(['name', 'rating']);
    $totalCompanies = DB::table('companies')->count();

if ($totalCompanies === 0) {
    return [
        "Training" => "00.00%",
        "Hiring" => "00.00%"
    ];
}

// Counting companies that contain "Training" in their 'type' array
$trainingCount = DB::table('companies')
                       ->where('type', 'LIKE', '%Training%')
                    ->count();
Log::info('Training companies count: ' . $trainingCount);

// Counting companies that contain "Hiring" in their 'type' array
$hiringCount = DB::table('companies')
                      ->where('type', 'LIKE', '%Hiring%')
                    ->count();

// Calculate the ratios
$trainingRatio = ($trainingCount / $totalCompanies) * 100;
$hiringRatio = ($hiringCount / $totalCompanies) * 100;

$companyTypeRatios = [
    trans('data.' . "Training") => number_format($trainingRatio, 2) . "%",
    trans('data.' . "Hiring") => number_format($hiringRatio, 2) . "%"
];
        $topSurveyCompanies = DB::table('surveys')
            ->join('companies', 'surveys.company_id', '=', 'companies.id')
            ->select('companies.name', DB::raw('count(surveys.id) as total'))
            ->groupBy('companies.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        $topRespondedSurveys = DB::table('surveys')
            ->leftJoin('votes', 'surveys.id', '=', 'votes.survey_id')
            ->select('surveys.title', DB::raw('count(votes.id) as responses'))
            ->groupBy('surveys.title')
            ->orderBy('responses', 'desc')
            ->limit(5)
            ->get();
        $mostFollowedCompanies = DB::table('followers')
            ->join('companies', 'followers.company_id', '=', 'companies.id')
            ->select('companies.name', DB::raw('count(followers.id) as followers'))
            ->groupBy('companies.name')
            ->orderBy('followers', 'desc')
            ->limit(5)
            ->get();
        $statistics = [
            'percentage_users_benefiting' => $percentage,
            'top_topics' => $topTopicsWithPercentages,
            'top_companies' => $topCompaniesWithPercentages,
            'jobsByLocation' => $jobsByLocation,
            'coursesByLocation' => $coursesByLocation,
            'topJobTopics' => $topJobTopics,
            'topCourseTopics' => $topCourseTopics,
            'jobsOnlineOfflineRatio' => $jobsOnlineOfflineRatio,
            'coursesOnlineOfflineRatio' => $coursesOnlineOfflineRatio,
            'topAdvisors' => $topAdvisors,
            'companyTypeRatios' => $companyTypeRatios,
            'topSurveyCompanies' => $topSurveyCompanies,
            'topRespondedSurveys' => $topRespondedSurveys,
            'mostFollowedCompanies' => $mostFollowedCompanies,
        ];
        return $this->sendResponse($statistics, 'statistics!', 200);
    }

}
