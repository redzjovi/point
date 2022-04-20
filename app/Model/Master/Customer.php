<?php

namespace App\Model\Master;

use App\Model\Accounting\ChartOfAccount;
use App\Model\Accounting\ChartOfAccountType;
use App\Model\Accounting\Journal;
use App\Model\MasterModel;
use App\Traits\Model\Master\CustomerJoin;
use App\Traits\Model\Master\CustomerRelation;

/**
 * @property int $id
 * @property null|string $code
 * @property null|string $tax_identification_number
 * @property string $name
 * @property null|string $address
 * @property null|string $city
 * @property null|string $state
 * @property null|string $country
 * @property null|string $zip_code
 * @property null|float $latitude
 * @property null|float $longitude
 * @property null|string $phone
 * @property null|string $phone_cc
 * @property null|string $email
 * @property null|string $notes
 * @property float $credit_limit
 * @property null|int $branch_id
 * @property null|int $created_by
 * @property null|int $updated_by
 * @property null|int $archived_by
 * @property null|string $created_at
 * @property null|string $updated_at
 * @property null|string $archived_at
 */
class Customer extends MasterModel
{
    use CustomerJoin, CustomerRelation;

    protected $connection = 'tenant';

    protected $appends = ['label'];

    protected $casts = ['credit_limit' => 'double'];

    protected $fillable = [
        'code',
        'name',
        'tax_identification_number',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'latitude',
        'longitude',
        'phone',
        'phone_cc',
        'email',
        'notes',
        'credit_limit',
        'pricing_group_id',
        'disabled',
    ];

    public static $morphName = 'Customer';

    public static $alias = 'customer';

    public function getLabelAttribute()
    {
        $label = $this->code ? '['.$this->code.'] ' : '';

        return $label.$this->name;
    }

    /**
     * Get the customer's total payable.
     */
    public function totalAccountPayable()
    {
        $payables = $this->journals()
            ->join(ChartOfAccount::getTableName(), ChartOfAccount::getTableName('id'), '=', Journal::getTableName('chart_of_account_id'))
            ->join(ChartOfAccountType::getTableName(), ChartOfAccountType::getTableName('id'), '=', ChartOfAccount::getTableName('type_id'))
            ->where(function ($query) {
                $query->where(ChartOfAccountType::getTableName('name'), '=', 'current liability')
                    ->orWhere(ChartOfAccountType::getTableName('name'), '=', 'long term liability')
                    ->orWhere(ChartOfAccountType::getTableName('name'), '=', 'other current liability');
            })
            ->selectRaw('SUM(`credit`) AS credit, SUM(`debit`) AS debit')
            ->first();

        return $payables->credit - $payables->debit;
    }

    /**
     * Get the customer's total receivable.
     */
    public function totalAccountReceivable()
    {
        $receivables = $this->journals()
            ->join(ChartOfAccount::getTableName(), ChartOfAccount::getTableName('id'), '=', Journal::getTableName('chart_of_account_id'))
            ->join(ChartOfAccountType::getTableName(), ChartOfAccountType::getTableName('id'), '=', ChartOfAccount::getTableName('type_id'))
            ->where(function ($query) {
                $query->where(ChartOfAccountType::getTableName('name'), '=', 'account receivable')
                    ->orWhere(ChartOfAccountType::getTableName('name'), '=', 'other account receivable');
            })
            ->selectRaw('SUM(`credit`) AS credit, SUM(`debit`) AS debit')
            ->first();

        return $receivables->debit - $receivables->credit;
    }
}
