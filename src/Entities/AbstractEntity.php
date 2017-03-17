<?php


namespace Mvdstam\Oauth2ServerLaravel\Entities;


use Illuminate\Database\Eloquent\Model;

abstract class AbstractEntity extends Model
{

    const TABLE_PREFIX = 'oauth2_';

    /**
     * @var bool
     */
    public $incrementing = false;

    protected $casts = [
        'id' => 'string'
    ];

    public function getTable()
    {
        return self::TABLE_PREFIX . parent::getTable();
    }

    public function joiningTable($related)
    {
        return self::TABLE_PREFIX . parent::joiningTable($related);
    }

}
