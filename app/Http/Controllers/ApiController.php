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
            withHeaders(['Authorization' => 'Bearer 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',
            'Content-Type' => 'application/json'])
        ->post('https://api.openai.com/v1/chat/completions',[
            'model' =>"gpt-3.5-turbo",
            "messages" => [$message],
            "temperature"=> 0.7
        ]);
        dd($response->body());
    }
}
