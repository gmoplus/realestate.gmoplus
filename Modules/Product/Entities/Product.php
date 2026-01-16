<?php

namespace Modules\Product\Entities;

use App\Models\UsedMedia;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\GST\Entities\GSTGroup;
use Modules\GST\Entities\GstTax;
use Modules\Seller\Entities\SellerProduct;
use Modules\Setup\Entities\Tag;
use Modules\Shipping\Entities\ProductShipping;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;
    protected $table = "products";
    protected $guarded = ["id"];
    protected $appends = [];
    public $translatable = [];
    protected $casts = [
        'id' => 'integer',
        'product_name' => 'string',
        'product_type' => 'integer',
        'variant_sku_prefix' => 'string',
        'unit_type_id' => 'integer',
        'brand_id' => 'integer',
        'thumbnail_image_source' => 'string',
        'media_ids' => 'string',
        'barcode_type' => 'string',
        'model_number' => 'string',
        'shipping_type' => 'integer',
        'shipping_cost' => 'double',
        "discount_type" => "integer",
        "discount" => 'double',
        "tax_type" => "string",
        'gst_group_id' => 'integer',
        'tax_id' => 'integer',
        'tax' => 'double',
        'pdf' => 'string',
        'video_provider' => 'string',
        'video_link' => 'string',
        'description' => 'string',
        'specification' => 'string',
        'minimum_order_qty' => 'integer',
        'max_order_qty' => 'integer',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_image' => 'string',
        'is_physical' => 'integer',
        'is_approved' => "integer",
        'status' => 'integer',
        'display_in_details' => 'integer',
        'requested_by' => 'integer',
        'created_by' => 'integer',
        'slug' => 'string',
        'updated_by' => 'integer',
        'stock_manage' => "string",
        'subtitle_1' => "string",
        'subtitle_2' => "string",
        'created_at' => "datetime",
        'updated_at' => "datetime",
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (isModuleActive('FrontendMultiLang')) {
            $this->translatable = ['product_name', 'subtitle_1', 'subtitle_2', 'description', 'specification', 'meta_title', 'meta_description'];
            $this->appends = ['translateProductName', 'TranslateProductSubtitle1', 'TranslateProductSubtitle2'];
        }
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if ($model->created_by == null) {
                $model->created_by = Auth::user()->id ?? null;
            }
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id ?? null;
        });
        self::creating(function ($model) {
            $model->slug = $model->createSlug($model->product_name);
        });
        self::created(function ($model) {
            Cache::forget('MegaMenu');
            Cache::forget('HeaderSection');
        });
        self::updating(function ($model) {
            $model->slug = $model->createSlug($model->product_name, $model->id);
        });
        self::updated(function ($model) {
            Cache::forget('MegaMenu');
            Cache::forget('HeaderSection');
        });
        self::deleted(function ($model) {
            Cache::forget('MegaMenu');
            Cache::forget('HeaderSection');
        });
        static::addGlobalScope('location', function (\Illuminate\Database\Eloquent\Builder $builder) {
            // Kullanıcının IP'sini al
            $ipAddress = request()->getClientIp();
            $locationData = self::getLocationFromIP($ipAddress);

            if ($locationData && !empty($locationData['country'])) {
                $country = strtolower($locationData['country']);
                $city = strtolower($locationData['city'] ?? '');

                //dd($country, $city);
                if($country == 'türkiye'){
                    $country = 'turkey';
                }

                // Eğer şehir mevcutsa, şehire göre filtre uygula, değilse sadece ülkeye göre
                $builder->where('country', 'LIKE', '%' . $country . '%')
                    ->where(function ($query) use ($city) {

                        if (!empty($city)) {
                            $query->where('country_level1', 'LIKE', '%' . $city . '%')
                                ->orWhereNull('country_level1');
                        }
                    });
            }
        });
    }
    public static function getLocationFromIP($ip)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json/".$ip."?lang=en");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function unit_type()
    {
        return $this->belongsTo(UnitType::class)->withDefault();
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class)->withPivot('category_id', 'product_id');
    }
    private function createSlug($name, $model = null)
    {
        $str_slug = Str::slug($name);
        return $this->abalivaslug($str_slug, 0, $model);
    }
    private function abalivaslug($slug, $count = 0, $model = null)
    {
        if ($count) {
            $newslug = $slug . '-' . $count;
        } else {
            $newslug = $slug;
        }
        if (static::whereSlug($newslug)->where('id', '!=', $model)->first()) {
            return $this->abalivaslug($slug, $count + 1, $model);
        }
        return $newslug;
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, "brand_id")->withDefault();
    }
    public function variations()
    {
        return $this->hasMany(ProductVariations::class);
    }
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
    public function activeSkus()
    {

        return $this->hasMany(ProductSku::class)->where('status', 1);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withPivot('tag_id', 'product_id');
    }
    public function gallary_images()
    {
        return $this->hasMany(ProductGalaryImage::class);
    }
    public function seller()
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }
    public function sellerProducts()
    {
        return $this->hasMany(SellerProduct::class, 'product_id', 'id');
    }
    public function relatedProducts()
    {
        return $this->hasMany(ProductRelatedSale::class, 'product_id', 'id');
    }
    public function upSales()
    {
        return $this->hasMany(ProductUpSale::class, 'product_id', 'id');
    }
    public function crossSales()
    {
        return $this->hasMany(ProductCrossSale::class, 'product_id', 'id');
    }
    public function shippingMethods()
    {
        return $this->hasMany(ProductShipping::class, 'product_id', 'id');
    }
    public function scopeBarcodeList($query)
    {
        return $array = array("C39", "C39+", "C39E", "C39E+", "C93", "I25", "POSTNET", "EAN2", "EAN5", "PHARMA2T");
    }
    public function gstGroup()
    {
        return $this->belongsTo(GSTGroup::class, 'gst_group_id', 'id');
    }
    public function gsttax()
    {
        return $this->belongsTo(GstTax::class, 'tax_id', 'id');
    }
    public function meta_image_media()
    {
        return $this->morphOne(UsedMedia::class, 'usable')->where('used_for', 'meta_image');
    }
    public function getTranslateProductNameAttribute()
    {
        return $this->attributes['product_name'];
    }
    public function getTranslateProductSubtitle1Attribute()
    {
        return $this->attributes['subtitle_1'];
    }
    public function getTranslateProductSubtitle2Attribute()
    {
        return $this->attributes['subtitle_2'];
    }
    public function getThumbnailImageSourceAttribute($value): string
    {
        return (string) $value;
    }
    public function getPdfAttribute($value): string
    {
        return (string) $value;
    }
    public function getMetaImageAttribute($value): string
    {
        return (string) $value;
    }
}
