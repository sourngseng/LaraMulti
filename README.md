## Step 1: Install Laravel 10 App

```
composer create-project --prefer-dist laravel/laravel LaraMulti
```

## Step 2: Connecting App to Database

```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=here your database name here
    DB_USERNAME=here database username here
    DB_PASSWORD=here database password here
```

## Step 3: Setting up migration and model

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('type')->default(0);
            /* Users: 0=>User, 1=>Admin, 2=>Manager */
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
```

-   Next open `app/User.php` and update the below field name is_admin here:

```
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *

     * @var array

     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array

     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array

     */
    protected $casts = [

        'email_verified_at' => 'datetime',
    ];

    /**
     * Interact with the user's first name.
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ["user", "admin", "manager"][$value],
        );
    }
}
```

-   Now, add is_admin filed after that will use the below command for creating this field into the database.

```
php artisan migrate
```

-   Then install laravel 9 UI in your project using the below command:

```
   composer require laravel/ui
```

```
   php artisan ui bootstrap --auth
```

-   Then execute the following commands:

```
    npm install
    npm run dev
```

## Step 4: Create Middleware and Setting up

```
php artisan make:middleware UserAccess
```

After creating a middleware go-to `app/Http/middleware`. Implement the logic here for checking a logged in users. Update the code in this handle function.

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $userType)
    {
        if(auth()->user()->type == $userType){
            return $next($request);
        }

        return response()->json(['You do not have permission to access for this page.']);
        /* return response()->view('errors.check-permission'); */
    }
}
```

-   Then register this middleware in the `app/Http/Kernel.php`. So, open kernal.php and add the following `$middlewareAliases` property in it:

```php
protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'user-access' => \App\Http\Middleware\UserAccess::class, //បញ្ចូល user-access
    ];
```

## Step 5: Define Route

-   Create routes and add it on web.php file as like below.
-   Open `routes/web.php` file

```php
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// All Normal Users Routes List
Route::middleware(['auth','user-access:user'])->group(function(){
    Route::get('/home',[HomeController::class,'index'])->name('home');
});

// All Admin Users Route List
Route::middleware(['auth','user-access:admin'])->group(function(){
    Route::get('/admin/home',[HomeController::class,'adminHome'])->name('admin.home');
});

// All Manager Route List

Route::middleware(['auth','user-access:manager'])->group(function(){
    Route::get('/manager/home',[HomeController::class,'managerHome'])->name('manager.home');
});

```

## Step 6: Create Methods in Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome()
    {
        return view('adminHome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managerHome()
    {
        return view('managerHome');
    }
}
```

## Step 7: Create Blade View

Now, create two blade view files first is display home page and second is display after login.

Open the `resources/views/home.blade`. file and update the below code.
