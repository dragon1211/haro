<?php

namespace App\Http\Middleware;
use App\Models\Customer;
use Closure;
use Config;
use App\Models\Manager;

class VerifyToken
{
    public function handle($request, Closure $next, $role)
    {
        switch ($role)
        {
        case 'client':
            return $this->verify_client_token($request, $next);
        case 'store':
            return $this->verify_store_token($request, $next);
        default:
            return response()->json([
                'result' => Config::get('constants.errno.E_INTERNAL'),
                'message' => 'Invalide middleware parameter.',
            ]);
        }
    }

    protected function verify_client_token($request, Closure $next)
    {
        $token = $request->header('x-access-token');
        $account = Customer::from_access_token($token);
        if (!isset($account))
            return response()->json([
                'result' => Config::get('constants.errno.E_TOKEN'),
                'message' => 'Invalide access token.',
            ]);
        $request->account = $account;
        return $next($request);
    }

    protected function verify_store_token($request, Closure $next)
    {
        $token = $request->header('x-access-token');
        $account = Manager::from_access_token($token);
        if (!isset($account))
            return response()->json([
                'result' => Config::get('constants.errno.E_TOKEN'),
                'message' => 'Invalide access token.',
            ]);
        $request->account = $account;

        return $next($request);
    }
}
