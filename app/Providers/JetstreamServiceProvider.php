<?php

namespace App\Providers;
use Illuminate\Support\Facades\App;
use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class JetstreamServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            $validate = [
                'email' => 'required|email',
                'password' => 'required',
            ];

            if(App::environment('production')) {
                $validate['g-recaptcha-response'] = 'required|captcha';
            
            }
            $request->validate($validate);
            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });
        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', __('Administrator'), [
            'create',
            'read',
            'update',
            'delete',
        ])->description(__('Administrator users can perform any action.'));

        Jetstream::role('editor', __('Editor'), [
            'read',
            'create',
            'update',
        ])->description(__('Editor users have the ability to read, create, and update.'));
    }

}
