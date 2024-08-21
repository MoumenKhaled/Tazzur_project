<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Job;
use App\Models\Course;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Manager;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->call(function () {
            $this->checkJobPostings();
        })->dailyAt('00:00')->when(function () {
            return now()->day % 3 === 0;
        });


        $schedule->call(function () {
            $this->checkCourses();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function checkJobPostings(NotificationService $notificationService)
    {
        $jobs = Job::where('stats', 'current')
            ->where('end_date', '>=', now())
            ->get();

        foreach ($jobs as $job) {
            $job->update(['status' => 'finite']);
            $job->save();

            //$this->sendJobNotification($job, $notificationService);
            $this->sendEndJobNotification($job, $notificationService);
        }
    }

    protected function checkCourses(NotificationService $notificationService)
    {
        $courses = Course::whereRaw('DATE_ADD(start_date, INTERVAL duration DAY) >= ?', [now()])
            ->where('status', '<>', 'current')
            ->get();


        foreach ($courses as $course) {
            $course->update(['status' => 'finite']);
            $this->sendEndCourseNotification($course, $notificationService);
            // if ($course->start_date->subDays(2)->isToday()) {

            //     $this->sendEndCourseNotification($course, $notificationService);
            // }
            // if ($course->start_date->isToday()) {
            //     $course->update(['status' => 'finite']);
            // }
        }
    }
    protected function sendJobNotification($job, $notificationService)
    {
        $messages = [
            'ar' => [
                'title' => 'تذكير بتصفية السيفيهات',
                'description' => "لقد مر شهر على نشر الفرصة {$job->job_title}. يرجى مراجعة السير الذاتية."
            ],
            'en' => [
                'title' => 'Reminder to Filter CVs',
                'description' => "A month has passed since the job {$job->job_title} was posted. Please review the CVs."
            ]
        ];

        $url = route('manager_applications_jobs', ['id' => $job->id]);

        $managers = Manager::where(function ($query) {
            $query->whereJsonContains('role_name', 'job_requests_coordinator')
                ->orWhereJsonContains('role_name', 'admin');
        })->get();


        foreach ($managers as $manager) {
            $notificationService->sendNotification($manager, $messages, $url, 'manager');
        }
    }
    protected function sendEndJobNotification($job, $notificationService)
    {
        $messages = [
            'ar' => [
                'title' => 'انتهت مدة عرض الفرصة',
                'description' => "لقد انتهت مدة عرض الفرصة {$job->job_title}. يرجى مراجعة السير الذاتية."
            ],
            'en' => [
                'title' => 'Opportunity Listing Expired',
                'description' => "The listing period for the job {$job->job_title} has expired. Please review the CVs."
            ]
        ];

        $url = route('manager_applications_jobs', ['id' => $job->id]);
        $company = $job->company;
        $notificationService->sendNotification($company, $messages, $url, 'company');


        // $managers = Manager::where(function ($query) {
        //     $query->whereJsonContains('role_name', 'job_requests_coordinator')
        //         ->orWhereJsonContains('role_name', 'admin');
        // })->get();


        // foreach ($managers as $manager) {
        //     $notificationService->sendNotification($manager, $messages, $url, 'company');
        // }
    }



    protected function sendCourseNotification($course, $notificationService)
    {
        $company = $course->company;
        $messages = [
            'ar' => [
                'title' => 'تذكير بمراجعة المتقدمين للدورة',
                'description' => "سيبدأ الكورس {$course->name} بعد يومين. يرجى مراجعة المتقدمين."
            ],
            'en' => [
                'title' => 'Reminder to Review Course Applicants',
                'description' => "The course {$course->name} will start in two days. Please review the applicants."
            ]
        ];

        $url = route('company_applications_course', ['id' => $course->id]);
        $notificationService->sendNotification($company, $messages, $url, 'company');
    }

    protected function sendEndCourseNotification($course, $notificationService)
    {
        $messages = [
            'ar' => [
                'title' => 'انتهت مدة عرض الكورس',
                'description' => "لقد انتهت مدة عرض الكورس {$course->job_title}. يرجى مراجعة المتقدمين ."
            ],
            'en' => [
                'title' => 'Course Listing Expired',
                'description' => "The listing period for the course {$course->job_title} has expired. Please review."
            ]
        ];

        $url = route('manager_applications_jobs', ['id' => $course->id]);
        $company = $course->company;
        $notificationService->sendNotification($company, $messages, $url, 'company');
    }
}
