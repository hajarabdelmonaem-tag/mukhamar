<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable(['title', 'description', 'image', 'link', 'position', 'sort_order', 'is_active'])]
class Banner extends Model
{
    /** @use HasFactory<BannerFactory> */
    use HasFactory, HasTranslations;

    /**
     * The translatable attributes.
     *
     * @var array<int, string>
     */
    public array $translatable = ['title', 'description'];

    /**
     * The full public URL of the banner image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return MediaHelper::toUrl($this->image);
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
