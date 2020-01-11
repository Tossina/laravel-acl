<?php

namespace Junges\ACL;

use DB;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class ACLAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*
         * Define the system permission gates
         */
        config('acl.models.permission') !== null
        ? $permissionModel = app(config('acl.models.permission'))
        : $permissionModel = app(\Junges\ACL\Http\Models\Permission::class);

        if ($this->checkConnectionStatus()) {
            if (config('acl.tables.permissions') !== null) {
                if (Schema::hasTable(config('acl.tables.permissions'))) {
                    $permissionModel->all()->map(function ($permission) {
                        Gate::define($permission->slug, fn($user) => $user->hasPermission($permission) || $user->isAdmin());
                    });
                }
            }
        }

        /*
         * Add blade directives
         */
        $this->registerBladeDirectives();
    }

    /**
     * Add custom blade directives.
     */
    private function registerBladeDirectives()
    {

        /*
         * Group directive
         */
        Blade::directive('group', fn($group) => "<?php if(auth()->check() && auth()->user()->hasGroup({$group})){?>");
        /*
         * Else group directive
         */
        Blade::directive('elsegroup', fn($group) => "<?php }else if(auth()->check() && auth()->user()->hasGroup({$group})){?>");
        /*
         * End group directive
         */
        Blade::directive('endgroup', fn() => '<?php } ?>');
        /*
         * Permission directive
         */
        Blade::directive('permission', fn($permission) => "<?php if(auth()->check() && auth()->user()->hasPermission({$permission})){?>");

        /*
         * Else permission directive
         */
        Blade::directive('elsepermission', fn($permission) => "<?php }else if(auth()->check() && auth()->user()->hasPermission({$permission})){?>");
        /*
         * End permission directive
         */
        Blade::directive('endpermission', fn() => '<?php } ?>');

        /*
         * All permissions directive
         */
        Blade::directive('allpermission', fn($permissions) => "<?php if(auth()->check() && auth()->user()->hasAllPermissions({$permissions})){?>");
        /*
         * End all permissions directive
         */
        Blade::directive('endallpermission', fn() => '<?php } ?>');

        /*
         * Any permission directive
         */
        Blade::directive('anypermission', fn($permissions) => "<?php if(auth()->check() && auth()->user()->hasAnyPermission({$permissions})){?>");
        /*
         * End any permission directive
         */
        Blade::directive('endanypermission', fn() => '<?php } ?>');

        /*
         * Any group directive
         */
        Blade::directive('anygroup', fn($groups) => "<?php if(auth()->check() && auth()->user()->hasAnyGroup({$groups})){?>");
        /*
         * End any group directive
         */
        Blade::directive('endanygroup', fn() => '<?php } ?>');

        /*
         * All groups directive
         */
        Blade::directive('allgroups', fn($groups) => "<?php if(auth()->check() && auth()->user()->hasAllGroups({$groups})){?>");
        /*
         * End all groups directive
         */
        Blade::directive('endallgroups', fn() => '<?php } ?>');
    }

    /**
     * Check for database connection.
     * @return bool
     */
    private function checkConnectionStatus()
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
