<?php

namespace Aripdev\Queryable;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

trait HasQueryable
{
    protected $builder;

    protected $isCountable = false;

    protected $withSorted = [];

    public function filtered($columns = [])
    {
        $sanitizeFilter = $this->checkAvailableField($columns);

        $queryLocal = null;

        if (!empty($columns) && !empty($sanitizeFilter)) {
            foreach ($sanitizeFilter as $key => $value) {
                if (!is_null($queryLocal)) {
                    $queryLocal->where($key, "=", $value);
                } else {
                    $queryLocal = $this->where($key, "=", $value);
                }
            }

            $this->mergeWhereBuilder($queryLocal);
        }

        return $this;
    }

    public function searched($columns = [])
    {
        $searchKey = $this->checkAvailableField(['q']);

        $keyword = !empty($searchKey) && strlen($searchKey['q']) < 255
            ? trim($searchKey['q']) : '';

        if (!empty($columns) && !empty($keyword)) {

            $queryLocal = $this->where(function ($query) use ($keyword, $columns) {

                foreach ($columns as $column) {
                    $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                }
            });

            $this->mergeWhereBuilder($queryLocal);
        }

        return $this;
    }

    public function sorted($columns = [])
    {
        $availableQuery = ['_sort', '_order'];

        $sanitizeSort = $this->checkAvailableField($availableQuery);

        if (!empty($columns) && $this->sortQueryExist($columns, $sanitizeSort)) {

            $order = $this->isDescOrder($sanitizeSort) ? 'desc' : 'asc';

            $this->withSorted = [$sanitizeSort['_sort'], $order];
        }

        return $this;
    }

    protected function isDescOrder($sanitizeSort)
    {
        return array_key_exists('_order', $sanitizeSort) && $sanitizeSort['_order'] == 'desc';
    }

    protected function sortQueryExist($columns, $sanitizeSort)
    {
        return !empty($sanitizeSort)
            && array_key_exists('_sort', $sanitizeSort)
            && in_array($sanitizeSort['_sort'], $columns);
    }

    protected function sortable()
    {
        $this->builder = is_null($this->builder)
            ? $this->orderBy($this->withSorted[0], $this->withSorted[1])
            : $this->builder->orderBy($this->withSorted[0], $this->withSorted[1]);
    }

    public function paginated()
    {
        $this->isCountable = true;

        return $this;
    }

    protected function paginable()
    {
        $availableQuery = ['_limit', '_page'];

        $filter = $this->checkAvailableField($availableQuery);

        $limit = array_key_exists('_limit', $filter) && ((int) $filter['_limit'] != 0)
            ? (int) $filter['_limit']
            : 10;

        $page = array_key_exists('_page', $filter) && ((int) $filter['_page'] != 0)
            ? (int) $filter['_page']
            : 1;

        $offset = ($limit * $page) - $limit;

        $this->builder = is_null($this->builder)
            ? $this->skip($offset)->take($limit)
            : $this->builder->skip($offset)->take($limit);
    }

    public function result()
    {
        if (!empty($this->withSorted)) {
            $this->sortable();
        }

        if ($this->isCountable) {

            $total = $this->builderCreator()->count();;

            App::make('xheader')->headers = ['X-Total-Count' => $total];

            $this->paginable();
        }

        return $this->builderCreator();
    }

    protected function builderCreator()
    {
        return is_null($this->builder) ? $this : $this->builder;
    }

    protected function checkAvailableField($filters)
    {
        $filtered = [];

        $query = Request::query();

        if (!empty($filters)) {

            foreach ($filters as $filter) {

                if (array_key_exists($filter, $query) && !is_null($query[$filter])) {
                    $filtered[$filter] = $query[$filter];
                }
            }
        }

        return $filtered;
    }

    protected function mergeWhereBuilder($queryLocal)
    {
        $this->builder = is_null($this->builder)
            ? $queryLocal
            : $this->builder->mergeConstraintsFrom($queryLocal);
    }
}
