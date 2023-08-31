<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \OpenAI;
use Illuminate\Support\Facades\Schema;


class OpenAIController extends Controller
{
    public function index() {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        
        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);

        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTables();
        $question = 'How many projects are there';
        // Initialize the response  
        $full_response = "";  
        
        // Now you have the complete generated SQL query
    
        $prompt = (string) view('ask-database::prompts.example', [
        'question' => $question,
        'tables' => $tables,
        ]);

        $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 300,
            // 'temperature' => 0.7,
            'top_p' => 1,
            // 'messages' => [
            //     ['role' => 'user', 'content' => $prompt],
            // ],
        ]);

        dd( $result['choices'][0]['text']); // an open-source, widely-used, server-side scripting language.
        }
}
