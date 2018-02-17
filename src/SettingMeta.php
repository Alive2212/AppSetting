<?php

namespace Alive2212\AppSetting;

use Illuminate\Database\Eloquent\Model;

class SettingMeta extends Model
{
    protected $table = 'setting_metas';


    protected $fillable = [
        'user_id',
        'key',
        'value',
        'extra_value',
        'scope',
        'revoked',
        ];

}
