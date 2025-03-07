<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    protected $fillable = ['employee_id', 'document_type'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('employeeDocuments');
    }
    public function getMediaUrl()
    {
        $media = $this->getFirstMedia('employeeDocuments');
        if ($media && File::exists($media->getPath())) {
            return $media->getUrl();
        }
        return asset('media/avatar.png');
    }
}
