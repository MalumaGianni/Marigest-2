<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Corso;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $corsi=Corso::all();
        return view('auth.register')->with(['corsi' => $corsi]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cognome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users','confirmed'],
            //'email_confirmation'=>['required','string','email','confirmed',,],
            'sezione_appartenenza' => ['required', 'string', 'max:255'],
            'comando_appartenenza' => ['string', 'max:255'],
            'grado' => ['string', 'required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        $token = Str::random(60);

        $user = User::create([
            'nome' => $request->nome,
            'cognome' => $request->cognome,
            'email' => $request->email,
            'sezione_appartenenza' => $request->sezione_appartenenza,
            'comando_appartenenza' => $request->comando_appartenenza,
            'grado' => $request->grado,
            'tipo_utente' => '0',
            'remember_token'  => hash('sha256', $token),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        //Auth::login($user);
        return view('auth.register')->with(["standby" => "Registrazione effettuata. Sei in attesa che il tuo account venga attivato"]);
        //return redirect(RouteServiceProvider::HOME);
    }
}
