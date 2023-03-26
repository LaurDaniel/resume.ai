<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function index(): string
    {
        return 'Success';
    }

    public function scanResume(ResumeRequest $request) {
        $data = $request->validated();
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($data['resume']);
        $textContent = $pdf->getText();
        $json_object = $data['json_object'] ?? '';
        $message = new \stdClass();
        $message->role = "user";
        $message->content = "Convert following text variable to json format matching the json validation after obj keyword: text='. $textContent.', obj='.$json_object.'";
        $response = Http::
            withHeaders(['Authorization' => 'Bearer '.env('OPEN_API_KEY'),
            'Content-Type' => 'application/json'])
        ->post('https://api.openai.com/v1/chat/completions',[
            'model' =>"gpt-3.5-turbo",
            "messages" => [$message],
            "temperature"=> 0.7
        ]);
//        TODO
//         1.De adaugat cazul in care nu este trimis json_object adica de pus conditie.
//         - In cazul in care este trimis, raspunsul gpt trebuie sa faca match cu obiectul, altfel sa fie un obiect json liber.
//         2.De adaugat in env OPEN_API_KEY
//         3.De acoperit cazul in care fisierul trimis nu pare a fi un cv
//         4.De intors ca raspuns in functie de cazuri
//         5.De facut refactor la controller

        dd($response->body());

    }
}
