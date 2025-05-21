<?php

namespace App\View\Components;

use App\DataTables\BaseDataTable;
use Illuminate\View\Component;

class DataTable extends Component
{
    public BaseDataTable $datatable;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(BaseDataTable $datatable)
    {
        $this->datatable = $datatable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.data-table');
    }
}
