<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;

class OperationLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TODO::改为配置
        if (1){
            $data = [
                'user_id' => auth()->id(),
                'username' => auth()->user()->username,
                'realname' => auth()->user()->realname,
                'ip' => $request->getClientIp(),
                'method' => $request->method(),
                'uri' => $request->path(),
                'query' => http_build_query($request->except(['password','_token'])),
            ];
            \App\Models\OperationLog::create($data);
        }
        return $next($request);
    }
}
