<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;


class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    public $timestamps = false;

    public static function getValue(string $key)
    {
        $setting = self::where('key', $key)->first();
        if ($setting)
            return optional($setting)->value;
        return null;
    }
    public function ScopeStoreValue($query, string $key, $value)
    {
        $setting = $query->where('key', $key)->first();
        $setting->update(['value' => $value]);
        return optional($setting)->value;
    }
}
