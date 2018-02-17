<?php

namespace Alive2212\AppSetting;

use Carbon\Carbon;

class AppSetting
{
    /**
     * @var bool
     */
    public static $isSettingPublished = false;

    /**
     * @var Carbon|null
     */
    protected $startDateTime;

    /**
     * @var null|String
     */
    protected $scope;

    /**
     * AppSetting constructor.
     * @param Carbon|null $startDateTime
     * @param String|null $scope
     */
    public function __construct(Carbon $startDateTime = null, String $scope = null)
    {
        // set start time
        if (is_null($startDateTime)) {
            $this->startDateTime = Carbon::now();
        } else {
            $this->startDateTime = $startDateTime;
        }

        // set scope if not null
        if (!is_null($scope)) {
            $this->scope = $scope;
        }
    }

    /**
     * @return static
     */
    public static function settingPublished()
    {
        static::$isSettingPublished = true;
        return new static;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        // get Setting Meta form Model with Array Type
        $params = $this->get(['key', 'value'])->toArray();

        // create multi dimension
        $data = [];
        foreach ($params as $param) {
            $value = $this->arrayMultiDimension($param['key'], $param['value']);
            $data = array_merge_recursive($value, $data);
        }
        return $data;
    }

    /**
     * @param $columns
     * @return \Illuminate\Support\Collection
     */
    public function get($columns = ['*'])
    {
        // return to array
        return $this->filter()->get($columns);
    }

    /**
     * @return SettingMeta|\Illuminate\Database\Query\Builder|static
     */
    public function filter()
    {
        // create Setting Meta from Model
        $settingMeta = new SettingMeta();

        $whereParams = [
            ['revoked', '<>', true],
        ];

        // add scope filter if not null
        if (!is_null($this->scope)) {
            array_push($whereParams, ['scope', 'like', "%\"" . $this->scope . "\"%"]);
        }

        // copy rules params
        $orWhereParams = $whereParams;

        // add created_at filter in whereParams
        array_push($whereParams, ['created_at', '>=', $this->startDateTime->toDateTimeString()]);

        // add created_at filter in orWhereParams
        array_push($orWhereParams, ['updated_at', '>=', $this->startDateTime->toDateTimeString()]);

        // get all setting from start time to now
        $settingMeta = $settingMeta->where($whereParams)->orWhere($orWhereParams);

        // return SettingMeta
        return $settingMeta;
    }

    /**
     * @param $stringKeys
     * @param $value
     * @return array
     */
    public function arrayMultiDimension($stringKeys, $value)
    {
        $keys = explode('.', $stringKeys);
        $record = [];
        if (count($keys) > 1) {
            $key = $keys[0];
            unset($keys[0]);
            $record[$key] = $this->arrayMultiDimension(implode('.', $keys), $value);
        } else {
            $record[$keys[0]] = $value;
        }
        return $record;
    }


    /**
     * @param array $params
     * @param String|null $lastKey
     * @param array $result
     * @return array
     */
    public function arraySingleDimension(Array $params, String $lastKey = null, $result = [])
    {
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                if (is_null($lastKey)) {
                    $result = $this->arraySingleDimension($paramValue, $paramKey, $result);
                } else {
                    $result = $this->arraySingleDimension($paramValue, $lastKey . '.' . $paramKey, $result);
                }
            } else {
                $result = array_merge($result, [$lastKey . '.' . $paramKey => $paramValue]);
            }
        }
        return $result;
    }

    /**
     * @param array $params
     * @param null $userID
     * @param null $extraValue
     * @param array|null $scope
     * @param bool $revoked
     * @return array
     */
    public function store(Array $params, $userID = null, $extraValue = null, Array $scope = null, bool $revoked = false)
    {
        $result = [];
        foreach ($params as $paramKey => $paramValue) {
            $settingMeta = new SettingMeta();
            array_push($result,
                $settingMeta->create([
                    'user_id' => $userID,
                    'key' => $paramKey,
                    'value' => $paramValue,
                    'extra_value' => $extraValue,
                    'scope' => $scope,
                    'revoked' => $revoked,
                ]));
        }
        return $result;
    }

    /**
     * @return Carbon
     */
    public function getStartDateTime(): Carbon
    {
        return $this->startDateTime;
    }

    /**
     *
     *
     * @param Carbon $startDateTime
     * @return $this
     */
    public function setStartDateTime(Carbon $startDateTime)
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }

    /**
     * @return String
     */
    public function getScope(): String
    {
        return $this->scope;
    }

    /**
     *
     *
     * @param String $scope
     * @return $this
     */
    public function setScope(String $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    public function clearDb(Carbon $createdAt = null)
    {
        $settingMeta = new SettingMeta();
        $filter = [];
        // Add filter if defined
        if (!is_null($createdAt)){
            array_push($filter, ['created_at','>=',$createdAt->toDateTimeString()]);
        }
        return $settingMeta->where($filter)->delete();
    }
}
