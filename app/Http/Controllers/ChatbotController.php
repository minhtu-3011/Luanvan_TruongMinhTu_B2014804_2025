<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatbotService;

class ChatbotController extends Controller
{
    protected $chatbot;

    public function __construct(ChatbotService $chatbot)
    {
        $this->chatbot = $chatbot;
    }

    public function message(Request $request)
    {

        $text = $request->input('message');

        // Quyền: chỉ admin/staff


        $answer = $this->chatbot->answer($text);

        return response()->json(['reply' => $answer]);
    }
}
