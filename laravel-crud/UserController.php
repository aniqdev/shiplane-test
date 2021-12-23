<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(15); // paginate() || simplePaginate()

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users|max:255|email',
            'phone' => 'min:10',
            'password' => 'required|min:6',
        ];

        $messages = [
            'email.required' => 'Введите имейл!',
            'email.email' => 'Не валидный имейл!',
            'email.unique' => 'Имейл занят!',
            'password.required' => 'Введите пароль!',
            'name.required' => 'Введите имя!',
            'phone.required' => 'Введите телефон!',
            'phone.min' => 'телефон минимум 10 символов',
        ];
        $validated = $request->validate($rules, $messages);

        $validated['password'] =  bcrypt($validated['password']);

        $user = new User($validated);
        
        if($user->save()) return redirect('/users')->with('info', 'Вы успешно зарегистрировались:)');
        return redirect()->route('home')->with('info', 'Возникли проблемы при регистрации!!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($user), 'email'],
            'phone' => 'required|min:10',
        ];

        $messages = [
            'name.required' => 'Введите имя!',
            'email.required' => 'Введите имейл!',
            'email.email' => 'Не валидный имейл!',
            'email.unique' => 'Имейл занят!',
            'phone.required' => 'Введите телефон!',
            'phone.min' => 'телефон минимум 10 символов',
        ];
        $validated = $request->validate($rules, $messages);

        if ($user->role !== 'admin' && $request->has('password') && $request->get('password')) {
            $rules = [
                'password' => 'required|min:6|confirmed',
                // 'password_confirmation' => 'required|min:6',
            ];
            $validated_p = $request->validate($rules);
            $validated['password'] =  bcrypt($validated_p['password']);
            // dd($validated);
        }
        
        // if ($request->hasFile('image')) {
        //     // $file = $request->file('image')->store('uploads', 'public');
        //     $file = $request->file('image')->store('images');
        //     $validated['image'] = $file;
        // }

        // dd($validated);

        if($user->update($validated)){
            // return redirect()->route('users.index')->with('status', 'Пост обнавлен!');
            return redirect()->back()->with('status', 'Пользователь обнавлен!');
        }else{
            return redirect()->back()->with('status', 'Пользователь НЕ обновлен! Свяжитесь с администратором');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('status', 'Нельзя удалить администратора!');
        }

        if($user->delete()){
            return redirect()->route('users.index')->with('status', 'Пользователь удален!');
        }else{
            return redirect()->back()->with('status', 'Ошибка при удалении! Обратитесь к администратору!');
        }
    }
}
