<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;

trait DatatablesFilterTrait
{
    /**
     * Set datatables filters from request for a ModelFilter.
     * Returns a ready-to-use stdClass
     *
     * This function must be used only when all filterProperties has been
     * set for the ModelFilter so it's data is ready to be fetch.
     *
     * @return stdClass
     */
    public function datatablesFilter(Request $request, array $searchColumns): \stdClass
    {
        $limit = $request->length;
        $skip = $request->start;
        $dir = $request->order[0]['dir'];
        $column_order = $request->columns[intval($request->order[0]['column'])]['name'];

        if (! empty($request->search['value'])) {
            if (! empty($searchColumns)) {
                $this->eloquentBuilder()->where(function ($q) use ($request, $searchColumns) {
                    foreach ($searchColumns as $searchColumn) {
                        $q->orWhere($searchColumn, 'LIKE', '%'.$request->search['value'].'%');
                    }
                });
            }
        }

        $data = new \stdClass();
        $data->data = [];
        $data->recordsTotal = $this->eloquentBuilder()->count();
        $data->recordsFiltered = $this->eloquentBuilder()->count();

        $this->eloquentBuilder()->take($limit)->skip($skip);
        $this->eloquentBuilder()->orderBy($column_order, $dir)->get();

        return $data;
    }
}
