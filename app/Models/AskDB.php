<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use \OpenAI;
use App\Exceptions\PotentiallyUnsafeQuery;

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
        //dd($query);
        //$query = str_replace(["\t", "\n", "\r"], '', $query);

        $result = json_encode(AskDB::getQueryResult($query));        

        $prompt = (string) view('prompts.answer', [
        'question' => $question,
        'result' => $result,
        ]);
        $prompt = str_replace(["\t", "\n", "\r"], '', $prompt);

        $answer = AskDB::queryOpenAi($client, $prompt);

        return $answer;
    }

    public static function getSQLQuery($question)
    {
        $table_list = ['phases', 'projects', 'tasks', 'users'];
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
        $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 300,
            'top_p' => 1,
        ]);
 
        $query = $result['choices'][0]['text']; 

        return $query;
    }

    protected static function ensureQueryIsSafe(string $query): void
    {
        if (! env('STRICT_MODE')) {
            return;
        }

        $query = strtolower($query);
        $forbiddenWords = ['insert', 'update', 'delete', 'alter', 'drop', 'truncate', 'create', 'replace'];
        throw_if(Str::contains($query, $forbiddenWords), PotentiallyUnsafeQuery::fromQuery($query));
    }

    protected static function getQueryResult(string $query): array
    {
        return DB::connection()->select($query);
    }

    protected static function getRawQuery(string $query): string
    {
        if (version_compare(app()->version(), '10.0', '<')) {
            /* @phpstan-ignore-next-line */
            return (string) DB::raw($query);
        }
        return DB::raw($query)->getValue(DB::connection()->getQueryGrammar());
    }
}
