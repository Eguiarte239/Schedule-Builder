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

    public static function getSQLQuery($question)
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        
        $yourApiKey = env("OPENAI_API_KEY");
        $client = OpenAI::client($yourApiKey);

        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTables();
    
        $prompt = (string) view('ask-database::prompts.example', [
        'question' => $question,
        'tables' => $tables,
        ]);

        $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 300,
            'top_p' => 1,
        ]);
 
        $query = $result['choices'][0]['text']; 
        AskDB::ensureQueryIsSafe($query);

        return $query;
    }

    protected function ensureQueryIsSafe(string $query): void
    {
        if (! env('STRICT_MODE')) {
            return;
        }

        $query = strtolower($query);
        $forbiddenWords = ['insert', 'update', 'delete', 'alter', 'drop', 'truncate', 'create', 'replace'];
        throw_if(Str::contains($query, $forbiddenWords), PotentiallyUnsafeQuery::fromQuery($query));
    }

    protected function evaluateQuery(string $query): object
    {
        dd(DB::connection()->select(AskDB::getRawQuery($query))[0] ?? new \stdClass());
        return DB::connection()->select(AskDB::getRawQuery($query))[0] ?? new \stdClass();
    }

    protected function getRawQuery(string $query): string
    {
        if (version_compare(app()->version(), '10.0', '<')) {
            /* @phpstan-ignore-next-line */
            return (string) DB::raw($query);
        }
        return DB::raw($query)->getValue(DB::connection()->getQueryGrammar());
    }
}
