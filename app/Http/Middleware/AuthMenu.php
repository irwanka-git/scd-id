<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use DB;
use Uuid;
use Session;

class AuthMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        

        if (!Auth::check()){
            return redirect()->guest('/home');
        }

        $path = $request->segment(1);

        if($path=='beranda' || $path=='' || $path=='user-guide' || $path=='akun-saya'){
            return $next($request);
        }


        $id_user = Auth::user()->id;
        $cek_menu = DB::select("
            select count(a.id_menu) as akses, 
            sum(b.ucc) as a_create, sum(b.ucu) as a_update ,sum(b.ucd) as a_delete 
            from menu as a, role_menu as b, user_role as c 
            where a.id_menu=b.id_menu and b.id_role = c.id_role 
            and a.url='$path' and c.id_user='$id_user' group by a.id_menu
            ");

        if (count($cek_menu)==0){
            return redirect()->guest('/');
        }else{
            $cek_menu = $cek_menu[0];
            $crud_akses = array('update'=>$cek_menu->a_update,
                            'create'=>$cek_menu->a_create,
                            'delete'=>$cek_menu->a_delete);
            Session::put('uxa894-'.$path,json_encode($crud_akses));
        }

        return $next($request);
    }
}
