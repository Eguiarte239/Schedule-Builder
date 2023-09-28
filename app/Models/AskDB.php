<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use \OpenAI;
use App\Exceptions\PotentiallyUnsafeQuery;
use PDOException;

class AskDB extends Model
{
    use HasFactory;

    // protected string $connection;

    public static function ask($question): string
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        
        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);

        $query = AskDB::getSQLQuery($question);

        // when query is too complex, return a message
        if(substr_count($query, "JOIN") > 1) {
            return "Muy difícil, krnal";
        }

        try {
            $result = json_encode(AskDB::getQueryResult($query));
        } catch(PDOException $e){
            return json_encode(['error' => 'Error en SQL']);
        }

        // when result of executed query is empty, return a message
        if($result === '[]') {
            return "No hay respuesta para esa pregunta";
        }
        
        $prompt = (string) view('prompts.answer', [
        'question' => $question,
        'result' => $result,
        ]);
        $prompt = str_replace(["\t", "\n", "\r"], ' ', $prompt);

        $answer = AskDB::queryOpenAi($client, $prompt);

        return $answer;
    }

    public static function getSQLQuery($question)
    {
        $table_list = ['projects','phases', 'tasks', 'users'];
        /* 'phases' => ['id', 'user_id', 'title', 'content', 'hour_estimate', 'start_date', 'end_date', 'priority', 'is_finished'], 
            'projects' => ['id', 'user_id', 'title', 'content', 'hour_estimate', 'start_date', 'end_date', 'priority', 'leader_id'],
            'tasks' => ['id', 'user_id', 'title', 'content', 'hour_estimate', 'start_date', 'end_date', 'priority', 'project_id', 'phase_id', 'user_id_assigned', 'predecessor_task', 'is_finished'],
            'users' => ['id', 'name', 'email'] */
        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);
        //$tables = Schema::getConnection()->getDoctrineSchemaManager()->listTables();
    
        $prompt = (string) view('prompts.sql-query', [
        'question' => $question,  
        'tables' => $table_list,
        ]);
        $prompt = str_replace(["\t", "\n", "\r"], '', $prompt);

        $query = AskDB::queryOpenAi($client, $prompt);
        AskDB::ensureQueryIsSafe($query);

        return $query;
    }

    protected static function queryOpenAi($client, $prompt)
    {
        /* $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 300,
            'top_p' => 1,
        ]); */
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.2,
            'frequency_penalty' => 0,
            'max_tokens' => 1200,
            'messages' => [['role' => 'user', 'content' => $prompt]]
        ]);
 
        $query = $result['choices'][0]['message']['content']; 

        return $query;
    }

    protected static function ensureQueryIsSafe(string $query): void
    {
        if (! env('STRICT_MODE')) {
            return;
        }

        $query = strtolower($query);
        $forbiddenWords = ['insert', 'update', 'delete', 'alter', 'drop', 'truncate', 'create', 'replace', 'schema', 'password', 'passwords', 'version', 'host', 'dump', 'debug', 'script', 'cookie', 'cookies', 'session'];
        throw_if(Str::contains($query, $forbiddenWords), PotentiallyUnsafeQuery::fromQuery($query));
    }

    protected static function getQueryResult(string $query): array
    {   
        //dd($query);
        return DB::connection()->select($query);
    }

}
