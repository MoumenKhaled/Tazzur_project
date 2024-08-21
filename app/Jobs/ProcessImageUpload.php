<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company;
    protected $image;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company, UploadedFile $image)
    {
        $this->company = $company;
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $allowedFileExtension = ['jpg', 'jpeg', 'png', 'bmp'];
        $extension = $this->image->getClientOriginalExtension();
        $check = in_array($extension, $allowedFileExtension);

        if ($check) {
            $imageName = "company{$this->company->id}." . $this->image->getClientOriginalExtension();
            $imagePath = "uploads/profiles/$imageName";
            $this->image->move(public_path('uploads/profiles'), $imageName);
            $this->company->logo = $imagePath;
            $this->company->save();
        }
    }
}
