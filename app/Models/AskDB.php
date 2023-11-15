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
        $tables = ['projects','phases', 'tasks', 'users'];
        $table_count = 0;

        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);

        $query = AskDB::getSQLQuery($question);
        
        $query = str_replace(["\t", "\n", "\r"], ' ', $query);

        // when query is too complex, return a message
        if(substr_count($query, "JOIN") > 1) {
            return trans("Your question exceed my current capacities. Please try with a simplier question.");
        }

        if($query === '[]') {
            return trans("It's possible that your question contains something not related to projects, phases or tasks. Please reformulate your question.");
        }
        foreach($tables as $table) {
            if(Str::contains($query, $table)) {
                $table_count += 1;
            }
        }

        if($table_count == 0 && !Str::contains($query, ["projects", "phases", "tasks", "users"])) {
            return trans("The question must involve at least projects, phases or tasks.");

        }

        try {
            $result = json_encode(AskDB::getQueryResult($query));
        } catch(PDOException $e){
            return trans("There was an unexpected error. Please try again in a moment or try to reformulate your question.");
        }

        // when result of executed query is empty, return a message
        if($result === '[]') {
            return trans("It's possible that there is no answer to your question. If you consider this is a mistake, try to reformulate your question.");
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
                    Note that questions related to users are valid but only if it is related to the system (projects, phases, or tasks),
                    you can only return the name and email related to a specific user if the question asks for it but not anything else.
                    If you are asked to return information such as a password or a session token then just say that you cannot return that information.'
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
        try{
            throw_if(Str::contains($query, $forbiddenWords), PotentiallyUnsafeQuery::fromQuery($query));
        } catch (PotentiallyUnsafeQuery $e) {
            return $e->getMessage();
        }
    }

    protected static function getQueryResult(string $query): array
    {
        return DB::connection()->select($query);
    }

}
