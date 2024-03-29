<?php


namespace App\Http\Filters;


use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{

    const CATEGORY_ID = 'category_id';

    protected function getCallbacks(): array
    {
        return [
            self::CATEGORY_ID => [$this, 'category_id']
        ];
    }

    public function categoryId(Builder $builder, $value){
        $builder->where('category_id', $value);
    }
}
