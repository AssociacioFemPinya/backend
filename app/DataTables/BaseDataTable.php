<?php

namespace App\DataTables;

use Illuminate\Http\Request;

abstract class BaseDataTable
{
    private string $id;

    private string $url;

    private string $title;

    private string $orderDir;

    private int $order;

    private array $columns;

    private array $searcheableColumns;

    private array $postAjaxData;

    private bool $embedded;

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id ?? 'no_id_set';
    }

    public function setUrl($url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        // TODO: How to raise a controlled exception if URL doesn't exist?
        return $this->url ?? '/';
    }

    public function setOrder($order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder(): string
    {
        return $this->order ?? 1;
    }

    public function setOrderDir($orderDir): self
    {
        $this->orderDir = $orderDir;

        return $this;
    }

    public function getOrderDir(): string
    {
        return $this->orderDir ?? 'asc';
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title ?? 'Missing title';
    }

    public function setPostAjaxData($postAjaxData): self
    {
        $this->postAjaxData = $postAjaxData;

        return $this;
    }

    public function getPostAjaxData(): array
    {
        return $this->postAjaxData ?? [];
    }

    public function setSearcheableColumns(array $searcheableColumns): self
    {
        $this->searcheableColumns = $searcheableColumns;

        return $this;
    }

    public function getSearcheableColumns(): array
    {
        return $this->searcheableColumns;
    }

    public function setEmbedded(bool $embedded): self
    {
        $this->embedded = $embedded;

        return $this;
    }

    public function addColumn(string $name, string $title, bool $orderable = false, $width = null): self
    {
        $this->columns[] = new Column(name: $name, title: $title, orderable: $orderable, width: $width);

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function isEmbedded(): bool
    {
        return $this->embedded ?? false;
    }

    // TODO: Filters should be constructed based on an abstract class to have well-known stable methods
    public function initData(Request $request, $filter): \stdClass
    {
        $limit = $request->length;
        $skip = $request->start;
        $dir = $request->order[0]['dir'];
        $column_order = $request->columns[intval($request->order[0]['column'])]['name'];

        if (! empty($request->search['value'])) {
            $searchColumns = $this->getSearcheableColumns();
            if (! empty($searchColumns)) {
                $filter->eloquentBuilder()->where(function ($q) use ($request, $searchColumns) {
                    foreach ($searchColumns as $searchColumn) {
                        $q->orWhere($searchColumn, 'LIKE', '%'.$request->search['value'].'%');
                    }
                });
            }
        }

        $data = new \stdClass();
        $data->data = [];

        $count = $filter->eloquentBuilder()->count();
        $data->recordsTotal = $count;
        $data->recordsFiltered = $count;

        $filter->eloquentBuilder()->take($limit)->skip($skip);
        $filter->eloquentBuilder()->orderBy($column_order, $dir)->get();

        return $data;
    }

    public function renderRows($renderFunction, $request, $filter, $params = []): \stdClass
    {
        $data = $this->initData($request, $filter);
        foreach ($filter->eloquentBuilder()->get() as $element) {
            array_push($data->data, $renderFunction($element, $params));
        }

        return $data;
    }

    public function addButton(array $btn_class, string $btn_iclass, array $data = [], ?string $a_href = null): string
    {
        $button = '<button class="btn '.implode(' ', $btn_class).' btn-action mr-5"';
        foreach ($data as $key => $value) {
            $button .= ' '.'data-'.$key.'='.$value;
        }
        $button .= '"><i class="'.$btn_iclass.'"></i></button>';

        if ($a_href != null) {
            $button = '<a href="'.$a_href.'">'.$button.'</a>';
        }

        return $button;
    }

    public function addViewButton(array $data = [], array $btn_class = [], ?string $a_href = null): string
    {
        return $this->addButton(['btn-info', ...$btn_class], 'fa-regular fa-eye', $data, $a_href);
    }

    public function addEditButton(array $data = [], array $btn_class = [], ?string $a_href = null): string
    {
        return $this->addButton(['btn-warning', ...$btn_class], 'fa-solid fa-pencil', $data, $a_href);
    }

    public function addDeleteButton(array $data = [], array $btn_class = [], ?string $a_href = null): string
    {
        return $this->addButton(['btn-danger', ...$btn_class], 'fa-solid fa-trash-can', $data, $a_href);
    }

    public function addNotificationButton(array $data = [], array $btn_class = [], ?string $a_href = null): string
    {
        return $this->addButton(['btn-info', ...$btn_class], 'fa-regular fa-envelope', $data, $a_href);
    }

    public function addHyperlink(string $url, string $text): string
    {
        return '<a href="'.$url.'">'.$text.'</a>';
    }
}
