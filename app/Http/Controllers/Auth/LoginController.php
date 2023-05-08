<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUser;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use App\Models\User;
use App\Models\PortalActivities;
use App\Repositories\UserRepository;
use Illuminate\Cache\RateLimiter;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller {

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $userRepository;

    public function __construct(
            UserRepository $userRepository
    ) {
        $this->middleware('guest')->except(['getLogout', 'getUserLogout', 'confirmEmail']);
        $this->userRepository = $userRepository;
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin($panel) {

       /* $ch = User::where('id', 1)->first();
        echo "<pre/>";

        print_r($ch->password);

        $ch->password = Hash::make("Admin@786$#");
        $ch->save();

        echo "<pre/><br/>";

        print_r($ch->password);
        exit;*/
        Session::put('panel', $panel);
        return view('admin.modules.auth.login', [
            'panel' => $panel,
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|AuthController
     */
    public function postLogin(LoginRequest $request, $panel) {
        $credentials = [
            'emailid' => $request->get('username'),
            'password' => $request->get('password'),
            'type' => 'super_admin',
            'user_status' => '1',
            'is_deleted' => '0'
        ];
        if (!Auth::validate($credentials)) {

            return redirect()->route('login', ['panel' => $panel])
                            ->with('fail_message', trans('auth.failed'));
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        // dd($user);
        // if (!$user->isEmailVerified()) {
        //     return redirect()->route('login', ['panel' => $panel])
        //         ->with('fail_message', trans('auth.please_confirm_your_email_first'));
        // }

        $loginsert = new PortalActivities();
        $loginsert->user_id = $user['id'];
        $loginsert->module_name = 'Login';
        $loginsert->request_data = 'NA';
        $loginsert->response_data = 'NA';
        $loginsert->created_at = date("Y-m-d H:i:s");
        $loginsert->updated_at = date("Y-m-d H:i:s");
        $loginsert->save();

        $user_id = $user['id'];

        Auth::login($user, config('custom.auth.remember_me') && $request->get('remember'));

        return $this->handleUserWasAuthenticated($request, $user);
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|AuthController
     */
    public function postUserLogin(LoginRequest $request) {
        $throttles = config('custom.auth.throttle_enabled');

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutUserResponse($request);
        }

        $credentials = $request->getCredentials();
        $validateCredentials = $this->userRepository->validateUser($credentials);

        if (!$validateCredentials) {
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            // $token = DB::table('password_resets')
            //     ->where('email', $credentials['email'])
            //     ->whereNotNull('token')
            //     ->first();
            // if ($token) {
            // $url = route('user-get-password-reset-link',$token->token);
            $url = route('user-password-reset-form');

            //$msg = "Sorry, as your account has been migrated to our new web platform automatically, you will have to change your password first for the security purposes! Click here to update your password now <a href='" . $url . "'>Link to Change Password</a>";
            $msg = "We have recently upgraded our system. Please change your password for security purposes by clicking <a href='" . $url . "'>here</a>";
            return redirect()->route('home')
                            ->with('fail_message', $msg);
            // }
            // return redirect()->route('home')
            //     ->with('fail_message', trans('auth.failed'));
        }
        $user = $this->userRepository->getByEmail($credentials['email']);

        if (!$user->isEmailVerified()) {
            return redirect()->route('home')
                            ->with('fail_message', trans('Your account is not activated yet. Please make sure to have clicked the activation link sent to your registered email address at the point of registration.'));
        }

        if ($user->is_active != 1) {
            return redirect()->route('home')
                            ->with('fail_message', trans('Sorry, your account has been deactivated by the Empower admin team! In case of any query, kindly reach out to our support team.'));
        }

        $role = $this->roleRepository->getByParams(['where' => ['name' => "candidate"]])->first();
        if (!in_array($role->name, $user->roles()->pluck('name')->toArray())) {
            return redirect()->route('home')
                            ->with('fail_message', trans('auth.failed'));
        }

        if ($user->isBlackListed()) {
            return redirect()->route('home')
                            ->with('fail_message', trans('Your account is banned! In case of any query, kindly reach out to our support team.'));
        }

        Auth::login($user, config('custom.auth.remember_me') && $request->get('remember'));

        if ($throttles) {
            $this->clearLoginAttempts($request);
        }
        $this->activityTrackerRepository->track($user->id, 'signed_in', 'log', $user);
        //event(new LoggedIn);
        if (Auth::user()->user_type == 'mplus') {
            return redirect()->route('front.m-plus-dashboard.index');
        }

        if ($user->last_login_at == null) {
            $user->last_login_at = Carbon::now();
            $user->save();
            return redirect()->route('front.profile.index');
            // return redirect()->route('front.dashboard.getSuperSenior');
        } else {
            return $this->authenticated($request, $this->guard()->user()) ?: redirect()->intended($this->redirectPath());
        }
    }

    private function redirectPath() {
        return route('front.dashboard.index');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  Request $request
     * @param  bool $throttles
     * @param $user
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $user) {
        return redirect()->route('admin.dashboard.index', ['panel' => Session::get('panel')]);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout($panel) {
        $user = Auth::user();
        if (!empty($user)) {
            $loginsert = new PortalActivities();
            $loginsert->user_id = $user['id'];
            $loginsert->module_name = 'Logout';
            $loginsert->request_data = 'NA';
            $loginsert->response_data = 'NA';
            $loginsert->created_at = date("Y-m-d H:i:s");
            $loginsert->updated_at = date("Y-m-d H:i:s");
            $loginsert->save();
        } else {
            $loginsert = new PortalActivities();
            $loginsert->user_id = "0";
            $loginsert->module_name = 'Logout-session-out';
            $loginsert->request_data = 'NA';
            $loginsert->response_data = 'NA';
            $loginsert->created_at = date("Y-m-d H:i:s");
            $loginsert->updated_at = date("Y-m-d H:i:s");
            $loginsert->save();
        }

        //event(new LoggedOut);
        Auth::logout();
        return redirect()->route('login', ['panel' => $panel]);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserLogout() {
        $user = Auth::user();
        $loginsert = new PortalActivities();
        $loginsert->user_id = $user['id'];
        $loginsert->module_name = 'Logout';
        $loginsert->request_data = 'NA';
        $loginsert->response_data = 'NA';
        $loginsert->created_at = date("Y-m-d H:i:s");
        $loginsert->updated_at = date("Y-m-d H:i:s");
        $loginsert->save();

        //event(new LoggedOut);
        if (Auth::user())
            $this->activityTrackerRepository->track(auth()->user()->id, 'signed_out', 'log', auth()->user());
        Session::flush();
        Auth::logout();
        return redirect()->route('home');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername() {
        return 'username';
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request) {
        return app(RateLimiter::class)->tooManyAttempts(
                        $request->input($this->loginUsername()) . $request->ip(),
                        $this->maxLoginAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function incrementLoginAttempts(Request $request) {
        app(RateLimiter::class)->hit(
                $request->input($this->loginUsername()) . $request->ip(),
                $this->lockoutTime() / 60
        );
    }

    /**
     * Determine how many retries are left for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function retriesLeft(Request $request) {
        $attempts = app(RateLimiter::class)->attempts(
                $request->input($this->loginUsername()) . $request->ip()
        );

        return $this->maxLoginAttempts() - $attempts + 1;
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request) {
        $seconds = app(RateLimiter::class)->availableIn(
                $request->input($this->loginUsername()) . $request->ip()
        );

        return redirect()->route('login', ['panel' => session('panel')])
                        ->withInput($request->only($this->loginUsername(), 'remember'))
                        ->with('fail_message', $this->getLockoutErrorMessage($seconds));
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutUserResponse(Request $request) {
        $seconds = app(RateLimiter::class)->availableIn(
                $request->input($this->loginUsername()) . $request->ip()
        );

        return redirect()->route('home')
                        ->withInput($request->only($this->loginUsername(), 'remember'))
                        ->with('fail_message', $this->getLockoutErrorMessage($seconds));
    }

    /**
     * Get the login lockout error message.
     *
     * @param  int  $seconds
     * @return string
     */
    protected function getLockoutErrorMessage($seconds) {
        return trans('auth.throttle', ['seconds' => $seconds]);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request) {
        app(RateLimiter::class)->clear(
                $request->input($this->loginUsername()) . $request->ip()
        );
    }

    /**
     * Get the maximum number of login attempts for delaying further attempts.
     *
     * @return int
     */
    protected function maxLoginAttempts() {
        return config('custom.auth.throttle_attempts', 3);
    }

    /**
     * The number of seconds to delay further login attempts.
     *
     * @return int
     */
    protected function lockoutTime() {
        $lockout = (int) config('custom.auth.throttle_lockout_time');

        if ($lockout <= 1) {
            $lockout = 1;
        }

        return 60 * $lockout;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister() {
        $socialProviders = config('auth.social.providers');
        return view('auth.register', compact('socialProviders'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request
     * @param RoleRepository $roles
     * @return \Illuminate\Http\Response
     */
    public function postRegister(RegisterUser $request) {
        $token = Str::random(32);
        $params = array();

        $params['first_name'] = request('first_name');
        $params['last_name'] = request('last_name');
        $params['user_type'] = "candidate";
        $params['email'] = request('email');
        $params['password'] = bcrypt(request('password'));
        $params['mobile'] = request('mobile');
        $params['confirmation_token'] = $token;
        $user = User::create($params);

        $this->activityTrackerRepository->track($user->id, 'registered', 'log', $user);

        $max = \DB::table('users')->where('user_type', 'candidate')->max('registration_id') + 1;
        $user->update([
            'registration_id' => $max,
        ]);
        $params = array();
        $params['user_id'] = $user->id;
        $params['country_id'] = request('country');
        $locations = $this->userLocationRepository->saveLocations($params);

        $role = $this->roleRepository->getByParams(['where' => ['name' => "candidate"]])->first();
        $user->roles()->sync($role->id);

        $requestParams = [
            'email' => request('email'),
            'link' => route('auth.confirmemail', $token),
            'first_name' => request('first_name'),
        ];
        $mail = Mail::to(request('email'))->send(new ConfirmEmail($requestParams));

        return redirect()->route('home')->with('success', trans('Registerd Successfully! Please verify email to continue.'));
    }

    /**
     * Confirm user's email.
     *
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($token) {
        $params = array();
        $params['where'] = ['confirmation_token' => $token];
        $user = $this->userRepository->getByParams($params)->first();

        if ($user) {
            $params = array();
            $params['id'] = $user->id;
            $params['email_verified_at'] = now();
            $params['status'] = 'new';
            $params['confirmation_token'] = null;
            $params['is_active'] = '1';
            $this->userRepository->save($params);

            $mail = Mail::to($user->email)->send(new WelcomeEmail($user));

            return redirect()->route('home')
                            ->withSuccess(trans('Your Email has been verified successfully.'));
        }

        return redirect()->route('home')
                        ->withErrors(trans('Your Email has not been verified successfully.'));
    }

    public function confirmUpdateEmail($token) {
        $params = array();
        $params['where'] = ['confirmation_token' => $token];
        $user = $this->userRepository->getByParams($params)->first();

        if ($user) {
            $params = array();
            $params['id'] = $user->id;
            $params['email_verified_at'] = now();
            $params['confirmation_token'] = null;
            $params['is_active'] = '1';
            $this->userRepository->save($params);

            return redirect()->route('home')
                            ->withSuccess(trans('Your Email has been verified successfully.'));
        }

        return redirect()->route('home')
                        ->withErrors(trans('Your Email has not been verified successfully.'));
    }

}
