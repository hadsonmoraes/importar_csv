<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class userController extends Controller
{
    //listar usuarios
    public function index()
    {

        $users = User::get();
        // dd($users);
        return view('users.index', ['users' => $users]);
    }

    public function import(Request $request)
    {
        $request->validate(
            [
                'file' => 'required|mimes:csv,txt|max:2048',

            ],
            [
                'file.required' => 'O campo arquivo é obrigatório',
                'file.mimes' => 'Arquivo inválido, necessario enviar arquivo CSV.',
                'file.max' => 'Tamanho do arquivo excede :max Mb.'
            ]
        );

        $headers = ['name', 'email', 'password'];

        $datafile = array_map('str_getcsv', file($request->file('file')));

        $numberRegisterRecords = 0;

        $emailAlreadyRegistered = false;

        foreach ($datafile as $keyData => $row) {
            $values = explode(';', $row[0]);

            foreach ($headers as $key => $header) {

                $arrayValues[$keyData][$header] = $values[$key];

                //verifica se é email
                if ($header == "email") {
                    if (User::where('email', $arrayValues[$keyData]['email'])->first()) {
                        $emailAlreadyRegistered .= $arrayValues[$keyData]['email'] . ",";
                    }
                }

                //verifica se é senha
                if ($header == "password") {
                    // criptografar senha existentes
                    // $arrayValues[$keyData][$header] = Hash::make($arrayValues[$keyData]['password'], ['rounds' => 12]);

                    $arrayValues[$keyData][$header] = Hash::make(Str::random(7), ['rounds' => 12]);
                }
            }
            $numberRegisterRecords++;
        }

        if ($emailAlreadyRegistered) {
            return back()->with('error', 'Dados não importados. Existem e-mails já cadastrados.:<br> ' . $emailAlreadyRegistered);
        }

        User::insert($arrayValues);

        return back()->with('sucess', 'Dados importados com sucesso.<br>Quantidade: ' . $numberRegisterRecords);
    }
}
