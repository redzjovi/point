<?php

namespace App\Model\Master;

use App\Model\MasterModel;
use App\Traits\Model\Master\ItemUnitJoin;
use App\Traits\Model\Master\ItemUnitRelation;

/**
 * @property int $id
 * @property string $label
 * @property string $name
 * @property string $converter
 * @property int $disabled
 * @property int $item_id
 * @property null|int $created_by
 * @property null|int $updated_by
 * @property null|int $created_at
 * @property null|int $updated_at
 */
class ItemUnit extends MasterModel
{
    use ItemUnitRelation, ItemUnitJoin;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'label',
        'converter',
        'item_id',
    ];

    protected $casts = [
        'converter' => 'double',
    ];

    public static $alias = 'item_unit';
}
