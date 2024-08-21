<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;


class ProcessDocumentUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company;
    protected $documentsKey;  // ????? ??????? ???

    public function __construct(Company $company, $cacheKey)
    {
        $this->company = $company;
        $this->documentsKey = $cacheKey;  // ???? ?? ????? ???? ??????? ???
    }


    /**
     * Execute the job.
     *
     * @return void
     */
   public function handle()
{
    $documentData = Cache::get($this->documentsKey);
    $paths = [];

    foreach ($documentData as $data) {
        $newPath = public_path('uploads/documents') . '/' . basename($data['path']);
        rename($data['path'], $newPath);
        $paths[] = $newPath;
    }

    $this->company->documents = json_encode($paths);
    $this->company->save();
}
}
