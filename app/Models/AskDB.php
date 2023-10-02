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
        $tables = ['projects','phases', 'tasks'];
        $table_count = 0;

        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);

        $query = AskDB::getSQLQuery($question);
        
        $query = str_replace(["\t", "\n", "\r"], ' ', $query);

        // when query is too complex, return a message
        if(substr_count($query, "JOIN") > 1) {
            return "Esta consulta supera mis capacidades actuales. Intenta con una consulta más simple.";
        }

        if($query === '[]') {
            return "Es posible que la pregunta contenga algo no relacionado a proyectos, fases, tareas o usuarios. Por favor reformula tu consulta.";
        }
        foreach($tables as $table) {
            if(Str::contains($query, $table)) {
                $table_count += 1;
            }
            if($table_count > 1 && !Str::contains($query, "WHERE")) {
                return "Para mis capacidades actuales solo puedes hacer consultas con una tabla a la vez si quieres obtener información de varios registros";
            }
        }

        if($table_count == 0 && !Str::contains($query, ["projects", "phases", "tasks", "users"])) {
            return "La consulta debe incluir al menos una de las tablas: projects, phases o tasks";

        }

        try {
            $result = json_encode(AskDB::getQueryResult($query));
        } catch(PDOException $e){
            return json_encode('Hubo un error inesperado. Por favor, intenta de nuevo en un momento o intenta reformular tu consulta.');
        }

        // when result of executed query is empty, return a message
        if($result === '[]') {
            return "Es posible que no haya respuesta a esa pregunta. Si consideras esto un error intenta reformular tu pregunta.";
        }

        $prompt = (string) view('prompts.answer', [
            'result' => $result,
        ]);
        $prompt = str_replace(["\t", "\n", "\r"], ' ', $prompt);

        $answer = AskDB::resultOpenAi($client, $prompt, $result);

        return $answer;
    }

    public static function getSQLQuery($question)
    {
        $table_list = ['projects','phases', 'tasks', 'users'];
        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);

        $prompt = (string) view('prompts.sql-query', [
        'question' => $question,
        'tables' => $table_list,
        ]);
      
        $query = AskDB::queryOpenAi($client, $prompt, $question);
        AskDB::ensureQueryIsSafe($query);

        return $query;
    }

    protected static function queryOpenAi($client, $prompt, $question)
    {
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.2,
            'frequency_penalty' => 0,
            'max_tokens' => 2000,
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'First of all think about the user question which is: '.$question.'
                    and then use the following prompt to know if it is a valid question: '.$prompt.'
                    Take your time to think about it to make sure this question follows all guidelines
                    described in the prompt provided to avoid unexpected behaviour. In case the question has
                    something to do with anything not related to the system as described in the prompt, then
                    return an empty array. If you need it then you can read the question and the prompt again
                    and use previous messages to detect what type of questions you must not approve.
                    Note that questions related to users are valid but only if it is related to the system (projects, phases or tasks)'
                ],
                [
                    'role' => 'user', 
                    'content' => $question
                ],
            ]
        ]);
 
        $query = $result['choices'][0]['message']['content']; 

        return $query;
    }

    protected static function resultOpenAi($client, $prompt, $result)
    {
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.2,
            'frequency_penalty' => 0,
            'max_tokens' => 2000,
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'First of all, take your time to make sure that the result you are going to return to
                    the user is correct based on the next prompt: '.$prompt.'.
                    If it is a valid message then you can proceed to return the result of the query in natural language.
                    Try to be as clear as possible and avoid using technical terms.'
                ],
                [
                    'role' => 'user', 
                    'content' => $result
                ],
            ]
        ]);

        $query = $result['choices'][0]['message']['content'];

        return $query;
    }

    protected static function ensureQueryIsSafe(string $query){

        if (! env('STRICT_MODE')) {
            return;
        }

        $query = strtolower($query);
        $forbiddenWords = ['insert', 'update', 'delete', 'alter', 'drop', 'truncate', 'create', 'replace', 'schema', 'password', 'passwords', 'version', 'host', 'dump', 'debug', 'script', 'cookie', 'cookies', 'session','* FROM users', '.password','* FROM personal_access_tokens'];
        // throw_if(Str::contains($query, $forbiddenWords), PotentiallyUnsafeQuery::fromQuery($query));
        try {
            throw_if(Str::contains($query, $forbiddenWords), PotentiallyUnsafeQuery::fromQuery($query));
        } catch (PotentiallyUnsafeQuery $exception) {
            return "Your query contains potentially unsafe words.";
        }
    }

    protected static function getQueryResult(string $query): array
    {
        return DB::connection()->select($query);
    }

}
