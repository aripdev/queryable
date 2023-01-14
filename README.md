# Laravel Queryable

This package handle common request for paginate,sort,filter and search data.

## Table of Contents

- [Getting started](#getting-started)
- [Inclusion](#inclusion)
- [Usage](#usage)
- [Methods](#methods)
    * [paginated](#paginated)
    * [sorted](#sorted)
    * [filtered](#filtered)
    * [searched](#searched)
- [Extras](#extras)


## Getting Started

Install package

``composer require aripdev/queryable``

## Inclusion

Add package to model that use queryable

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Aripdev\Queryable\HasQueryable;

class User extends Authenticatable
{
    use HasQueryable, HasApiTokens, HasFactory;
}
```

## Usage

This is how you can call some methods in your controller.

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LabController extends Controller
{
    public function index(User $user)
    {
        return $user->paginated()
            ->sorted()
            ->result(['name'])
            ->get();
    }
}
```

## Methods

Available method

### paginated

- Controller

    ```php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;

    class LabController extends Controller
    {
        public function index(User $user)
        {
            return $user->paginated()
                ->result()
                ->get();
        }
    }
    ```

- Url parameter `_limit` and `_page` to limit your data.

    ex : `yourdomain/user?_page=1&_limit=5`

note: default limit of paginated is 10 if user not bring paramater for ``_limit`` in request

### sorted

- Controller

    ```php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;

    class LabController extends Controller
    {
        public function index(User $user)
        {
            return $user
                ->paginated()
                ->sorted(['name'])
                ->result()
                ->get();
        }
    }
    ```

    make sure the column exist in model by passing paramereter method,this example that we have column name in our model.

- Url parameter `_sort` and optionaly `_order`, default sorting is ascending

    ex : ``yourdomain/user?_sort=name&_order=desc&_limit=2``

### filtered

- controller

    ```php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;

    class LabController extends Controller
    {
        public function index(User $user)
        {
            return $user
                ->paginated()
                ->sorted(['name'])
                ->filtered(['role'])
                ->result()
                ->get();
        }
    }
    ```
- Url parameter that configure column in method filtered, in this example we filtered data with column `role`

    ex: ``yourdomain/user?role=developer&_limit=2``

### searched

- Controller

    ```php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;

    class LabController extends Controller
    {
        public function index(User $user)
        {
            return $user
                ->paginated()
                ->sorted(['name'])
                ->filtered(['role'])
                ->searched(['title'])
                ->result()
                ->get();
        }
    }
    ```
- Url parameter like filtered method put parameter column that we want to search and we use column ``title`` and put the value `q` in url

    ex: ``yourdomain/user?q=enginering``

## Extras

if you want to show the count data of every query,you can use `xheader`

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LabController extends Controller
{
    public function index(User $user)
    {
        $data = $user
            ->paginated()
            ->sorted(['name'])
            ->filtered(['role'])
            ->searched(['title'])
            ->result()
            ->get();

        $xTotalCount = app('xheader')->headers;

        return response($data, 200, $xTotalCount);
    }
}
```









