<?php

namespace App\Providers;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Auht; // Assuming you have a model for API keys

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
//  public function boot()
// {
//     $this->app['auth']->viaRequest('api', function ($request) {
//         $header = $request->header('Authorization');

        

//         if ($header && $header === 'chyhgyyy00h2yhjgfhghdddsaaerdfdgffghhgdjfhgjfhgjfhkjf') {
//             return new User(); 
//         }

//         throw new HttpResponseException(response()->json([
//             'status' => false,
//             'message' => 'Authorization api-key ไม่ถูกต้องหรือไม่มี'
//         ], 401));
//     });  
// }
// }



public function boot()
{
    $this->app['auth']->viaRequest('api', function ($request) {
        $token = $request->header('Authorization');

        if (!$token) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'กรุณาใส่ Authorization api-key ใน Header'
            ], 401));
        }

        $authRecord = Auht::where('token', $token)->first();

        if ($authRecord) {
            // คุณสามารถคืนค่าผู้ใช้จริงจาก token ได้ เช่น:
            return new User();
        }

        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Authorization api-key ไม่ถูกต้องหรือไม่มี'
        ], 401));
    });
}
}
// หรือเชื่อมกับฐานข้อมูลจริง เช่น:
// $this->app['auth']->viaRequest('api', function ($request) {
//     $token = $request->header('Authorization');

//     return \App\Models\User::where('api_token', str_replace('Bearer ', '', $token))->first();
// });

