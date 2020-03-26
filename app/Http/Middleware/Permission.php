<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 2020/3/26
 * Time: 13:57
 */

namespace App\Http\Middleware;

use App\User;
use Closure;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Permission extends PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        /** @var User $op_user */
        $op_user = auth()->user();
        if(empty($op_user['currentMainUser'])){
            throw new HttpException(403, 'Please select the advertiser of the service');
        } else if(!$op_user['currentMainUser']['isAdvertiseEnabled']){
            throw new HttpException(403,
                'The selected advertiser has no permission to advertise');
        }
        return parent::handle($request, $next, $permission); // TODO: Change the autogenerated stub
    }
}